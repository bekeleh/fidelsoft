<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

class Google_Service_AndroidPublisher_ProductBill extends Google_Model
{
  public $acknowledgementState;
  public $consumptionState;
  public $developerPayload;
  public $kind;
  public $orderId;
  public $productId;
  public $BillState;
  public $BillTimeMillis;
  public $BillToken;
  public $BillType;
  public $quantity;

  public function setAcknowledgementState($acknowledgementState)
  {
    $this->acknowledgementState = $acknowledgementState;
  }
  public function getAcknowledgementState()
  {
    return $this->acknowledgementState;
  }
  public function setConsumptionState($consumptionState)
  {
    $this->consumptionState = $consumptionState;
  }
  public function getConsumptionState()
  {
    return $this->consumptionState;
  }
  public function setDeveloperPayload($developerPayload)
  {
    $this->developerPayload = $developerPayload;
  }
  public function getDeveloperPayload()
  {
    return $this->developerPayload;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setOrderId($orderId)
  {
    $this->orderId = $orderId;
  }
  public function getOrderId()
  {
    return $this->orderId;
  }
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  public function getProductId()
  {
    return $this->productId;
  }
  public function setBillState($BillState)
  {
    $this->BillState = $BillState;
  }
  public function getBillState()
  {
    return $this->BillState;
  }
  public function setBillTimeMillis($BillTimeMillis)
  {
    $this->BillTimeMillis = $BillTimeMillis;
  }
  public function getBillTimeMillis()
  {
    return $this->BillTimeMillis;
  }
  public function setBillToken($BillToken)
  {
    $this->BillToken = $BillToken;
  }
  public function getBillToken()
  {
    return $this->BillToken;
  }
  public function setBillType($BillType)
  {
    $this->BillType = $BillType;
  }
  public function getBillType()
  {
    return $this->BillType;
  }
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  public function getQuantity()
  {
    return $this->quantity;
  }
}
