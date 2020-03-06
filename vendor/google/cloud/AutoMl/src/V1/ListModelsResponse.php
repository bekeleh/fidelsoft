<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/automl/v1/service.proto

namespace Google\Cloud\AutoMl\V1;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Response message for
 * [AutoMl.ListModels][google.cloud.automl.v1.AutoMl.ListModels].
 *
 * Generated from protobuf message <code>google.cloud.automl.v1.ListModelsResponse</code>
 */
class ListModelsResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * List of models in the requested page.
     *
     * Generated from protobuf field <code>repeated .google.cloud.automl.v1.Model model = 1;</code>
     */
    private $model;
    /**
     * A token to retrieve next page of results.
     * Pass to
     * [ListModelsRequest.page_token][google.cloud.automl.v1.ListModelsRequest.page_token]
     * to obtain that page.
     *
     * Generated from protobuf field <code>string next_page_token = 2;</code>
     */
    private $next_page_token = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Google\Cloud\AutoMl\V1\Model[]|\Google\Protobuf\Internal\RepeatedField $model
     *           List of models in the requested page.
     *     @type string $next_page_token
     *           A token to retrieve next page of results.
     *           Pass to
     *           [ListModelsRequest.page_token][google.cloud.automl.v1.ListModelsRequest.page_token]
     *           to obtain that page.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Automl\V1\Service::initOnce();
        parent::__construct($data);
    }

    /**
     * List of models in the requested page.
     *
     * Generated from protobuf field <code>repeated .google.cloud.automl.v1.Model model = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * List of models in the requested page.
     *
     * Generated from protobuf field <code>repeated .google.cloud.automl.v1.Model model = 1;</code>
     * @param \Google\Cloud\AutoMl\V1\Model[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setModel($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Cloud\AutoMl\V1\Model::class);
        $this->model = $arr;

        return $this;
    }

    /**
     * A token to retrieve next page of results.
     * Pass to
     * [ListModelsRequest.page_token][google.cloud.automl.v1.ListModelsRequest.page_token]
     * to obtain that page.
     *
     * Generated from protobuf field <code>string next_page_token = 2;</code>
     * @return string
     */
    public function getNextPageToken()
    {
        return $this->next_page_token;
    }

    /**
     * A token to retrieve next page of results.
     * Pass to
     * [ListModelsRequest.page_token][google.cloud.automl.v1.ListModelsRequest.page_token]
     * to obtain that page.
     *
     * Generated from protobuf field <code>string next_page_token = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setNextPageToken($var)
    {
        GPBUtil::checkString($var, True);
        $this->next_page_token = $var;

        return $this;
    }

}

