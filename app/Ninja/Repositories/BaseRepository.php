<?php

namespace App\Ninja\Repositories;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;

/**
 * Class BaseRepository.
 */
class BaseRepository
{

    public function getClassName()
    {
        return null;
    }

    private function getInstance()
    {
        $className = $this->getClassName();

        return new $className();
    }

    private function getEventClass($entity, $type)
    {
        $entityType = $entity->getEntityType();

        if (in_array($entityType, [ENTITY_USER])) {
            return 'App\Events\User\\' . ucfirst($entityType) . 'Was' . $type . 'Event';
        } elseif (in_array($entityType, [ENTITY_INVOICE, ENTITY_RECURRING_INVOICE, ENTITY_QUOTE, ENTITY_PAYMENT])) {
            return 'App\Events\Sale\\' . ucfirst($entityType) . 'Was' . $type . 'Event';
        } elseif (in_array($entityType, [ENTITY_CLIENT, ENTITY_CREDIT])) {
            return 'App\Events\Client\\' . ucfirst($entityType) . 'Was' . $type . 'Event';
        } elseif (in_array($entityType, [ENTITY_BILL, ENTITY_RECURRING_INVOICE, ENTITY_BILL_QUOTE, ENTITY_BILL_PAYMENT])) {
            return 'App\Events\Purchase\\' . ucfirst($entityType) . 'Was' . $type . 'Event';
        } elseif (in_array($entityType, [ENTITY_VENDOR, ENTITY_VENDOR_CREDIT])) {
            return 'App\Events\Vendor\\' . ucfirst($entityType) . 'Was' . $type . 'Event';
        } elseif (in_array($entityType, [ENTITY_EXPENSE])) {
            return 'App\Events\Expense\\' . ucfirst($entityType) . 'Was' . $type . 'Event';
        } else {
            return 'App\Events\\' . ucfirst($entityType) . 'Was' . $type . 'Event';
        }

    }

    public function archive($entity)
    {
        if ($entity->trashed()) {
            return;
        }

        $entity->delete();

        $className = $this->getEventClass($entity, 'Archived');

        if (class_exists($className)) {
            event(new $className($entity));
        }
    }

    public function restore($entity)
    {
        if (!$entity->trashed()) {
            return;
        }

        if ($entity->getEntityType() === ENTITY_INVOICE) {
            $invoiceLineItems = isset($entity) ? $entity->invoice_items()->get()->toArray() : '';
            $this->adjustInvoiceItems($invoiceLineItems);
        } elseif ($entity->getEntityType() === ENTITY_BILL) {
            $billLineItems = isset($entity) ? $entity->invoice_items()->get()->toArray() : '';
            $this->restoreBillItems($billLineItems);
        }

        $fromDeleted = false;
        $entity->restore();

        if ($entity->is_deleted) {
            $fromDeleted = true;
            $entity->is_deleted = false;
            $entity->save();
        }

        $className = $this->getEventClass($entity, 'Restored');

        if (class_exists($className)) {
            event(new $className($entity, $fromDeleted));
        }
    }

    public function delete($entity)
    {
        if ($entity->is_deleted) {
            return;
        }

        if ($entity->getEntityType() === ENTITY_INVOICE) {
            $invoiceLineItems = isset($entity) ? $entity->invoice_items()->get()->toArray() : '';
            $this->restoreInvoiceItems($invoiceLineItems);
        } elseif ($entity->getEntityType() === ENTITY_BILL) {
            $billLineItems = isset($entity) ? $entity->invoice_items()->get()->toArray() : '';
            $this->adjustBillItems($billLineItems);
        }
        $entity->is_deleted = true;
        $entity->save();

        $entity->delete();

        $className = $this->getEventClass($entity, 'Deleted');

        if (class_exists($className)) {
            event(new $className($entity));
        }
    }

    public function bulk($ids, $action)
    {
        if (!$ids) {
            return 0;
        }

        $entities = $this->findByPublicIdsWithTrashed($ids);

        foreach ($entities as $entity) {
            if (Auth::user()->can('edit', $entity)) {
                $this->$action($entity);
            }
        }

        return count($entities);
    }

    public function findByPublicIds($ids)
    {
        return $this->getInstance()->scope($ids)->get();
    }

    public function findByPublicIdsWithTrashed($ids)
    {
        return $this->getInstance()->scope($ids)->withTrashed()->get();
    }

    protected function applyFilters($query, $entityType, $table = null)
    {
        $table = Utils::pluralizeEntityType($table ?: $entityType);
        if ($filter = session('entity_state_filter:' . $entityType, STATUS_ACTIVE)) {

            $filters = explode(',', $filter);
            $query->where(function ($query) use ($filters, $table) {
                $query->whereNull($table . '.id');

                if (in_array(STATUS_ACTIVE, $filters)) {
                    $query->orWhereNull($table . '.deleted_at');
                }
                if (in_array(STATUS_ARCHIVED, $filters)) {
                    $query->orWhere(function ($query) use ($table) {
                        $query->whereNotNull($table . '.deleted_at');

                        if (!in_array($table, ['users'])) {
                            $query->where($table . '.is_deleted', 0);
                        }
                    });
                }
                if (in_array(STATUS_DELETED, $filters)) {
                    $query->orWhere(function ($query) use ($table) {
                        $query->whereNotNull($table . '.deleted_at')
                            ->where($table . '.is_deleted', 1);
                    });
                }
            });
        }

        return $query;
    }
}
