<?php declare(strict_types=1);

namespace FWK\Core\Controllers\Traits;

use SDK\Core\Dtos\Element;
use FWK\Core\Resources\Utils;
use FWK\Enums\LanguageLabels;
use FWK\Enums\Parameters;
use SDK\Services\Parameters\Groups\Basket\RewardPointsRedeemParametersGroup;

/**
 * This is the redeem reward points trait.
 *
 * @see RedeemRewardPointsTrait::getRedeemRewardPointsResponseData()
 *
 * @package FWK\Core\Controllers\Traits
 */
trait RedeemRewardPointsTrait {

    /**
     * Returns the response data for the redeem reward points actions.
     * 
     * @return Element|NULL
     */
    protected function getRedeemRewardPointsResponseData(int $id = 0, int $value = 0): ?Element {
        $rewardPointsRedeemParametersGroup = new RewardPointsRedeemParametersGroup();

        if ($id === 0) {
            $id = intval($this->getRequestParam(Parameters::ID, true));
        }
        $rewardPointsRedeemParametersGroup->setId($id);
        if ($value === 0) {
            $value = intval($this->getRequestParam(Parameters::VALUE, false, 0));
        }
        $rewardPointsRedeemParametersGroup->setValue($value);
        $response = $this->basketService->redeemRewardPoints($rewardPointsRedeemParametersGroup);

        if($value === 0 ){
            $this->responseMessage = $this->language->getLabelValue(LanguageLabels::REWARD_POINTS_DELETED, $this->responseMessage);
        }else{
            $this->responseMessage = $this->language->getLabelValue(LanguageLabels::REWARD_POINTS_ADDED, $this->responseMessage);            
        }
        

        $this->responseMessageError = Utils::getErrorLabelValue($response);
        return $response;
    }
}
