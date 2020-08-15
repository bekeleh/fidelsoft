<?php
namespace Aws\CostExplorer;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Cost Explorer Service** service.
 * @method \Aws\Result createCostCategoryDefinition(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createCostCategoryDefinitionAsync(array $args = [])
 * @method \Aws\Result deleteCostCategoryDefinition(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteCostCategoryDefinitionAsync(array $args = [])
 * @method \Aws\Result describeCostCategoryDefinition(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeCostCategoryDefinitionAsync(array $args = [])
 * @method \Aws\Result getCostAndUsage(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getCostAndUsageAsync(array $args = [])
 * @method \Aws\Result getCostAndUsageWithResources(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getCostAndUsageWithResourcesAsync(array $args = [])
 * @method \Aws\Result getCostForecast(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getCostForecastAsync(array $args = [])
 * @method \Aws\Result getDimensionValues(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getDimensionValuesAsync(array $args = [])
 * @method \Aws\Result getReservationCoverage(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getReservationCoverageAsync(array $args = [])
 * @method \Aws\Result getReservationBillRecommendation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getReservationBillRecommendationAsync(array $args = [])
 * @method \Aws\Result getReservationUtilization(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getReservationUtilizationAsync(array $args = [])
 * @method \Aws\Result getRightsizingRecommendation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getRightsizingRecommendationAsync(array $args = [])
 * @method \Aws\Result getSavingsPlansCoverage(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getSavingsPlansCoverageAsync(array $args = [])
 * @method \Aws\Result getSavingsPlansBillRecommendation(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getSavingsPlansBillRecommendationAsync(array $args = [])
 * @method \Aws\Result getSavingsPlansUtilization(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getSavingsPlansUtilizationAsync(array $args = [])
 * @method \Aws\Result getSavingsPlansUtilizationDetails(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getSavingsPlansUtilizationDetailsAsync(array $args = [])
 * @method \Aws\Result getTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getTagsAsync(array $args = [])
 * @method \Aws\Result getUsageForecast(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getUsageForecastAsync(array $args = [])
 * @method \Aws\Result listCostCategoryDefinitions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listCostCategoryDefinitionsAsync(array $args = [])
 * @method \Aws\Result updateCostCategoryDefinition(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateCostCategoryDefinitionAsync(array $args = [])
 */
class CostExplorerClient extends AwsClient {}
