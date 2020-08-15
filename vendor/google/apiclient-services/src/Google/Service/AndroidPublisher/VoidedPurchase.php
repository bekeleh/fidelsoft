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

class Google_Service_AndroidPublisher_VoidedBill extends Google_Model
{
  public $kind;
  public $orderId;
  public $BillTimeMillis;
  public $BillToken;
  public $voidedReason;
  public $voidedSource;
  public $voidedTimeMillis;

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
  public function setVoidedReason($voidedReason)
  {
    $this->voidedReason = $voidedReason;
  }
  public function getVoidedReason()
  {
    return $this->voidedReason;
  }
  public function setVoidedSource($voidedSource)
  {
    $this->voidedSource = $voidedSource;
  }
  public function getVoidedSource()
  {
    return $this->voidedSource;
  }
  public function setVoidedTimeMillis($voidedTimeMillis)
  {
    $this->voidedTimeMillis = $voidedTimeMillis;
  }
  public function getVoidedTimeMillis()
  {
    return $this->voidedTimeMillis;
  }
}
