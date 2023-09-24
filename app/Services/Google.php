<?php

namespace App\Services;

use App\Models\GaProfile;

class Google
{
    /**
     * @param $analytics
     * @param $connectId
     * @return void
     */
    public static function getProfileId($analytics, $connectId)
    {
        // Get the list of accounts for the authorized user.
        $accounts = $analytics->management_accounts->listManagementAccounts();

        if (count($accounts->getItems()) > 0) {
            foreach ($accounts->getItems() as $item) {
                $firstAccountId = $item->getId();

                // Get the list of properties for the authorized user.
                $properties = $analytics->management_webproperties
                    ->listManagementWebproperties($firstAccountId);

                if (count($properties->getItems()) > 0) {
                    $items = $properties->getItems();
                    $firstPropertyId = $items[0]->getId();

                    // Get the list of views (profiles) for the authorized user.
                    $profiles = $analytics->management_profiles
                        ->listManagementProfiles($firstAccountId, $firstPropertyId);

                    if (count($profiles->getItems()) > 0) {
                        $items = $profiles->getItems();

                        GaProfile::updateOrCreate([
                            'connect_id' => $connectId,
                            'profile_id' => $items[0]->getId()
                        ], [
                            'name' => $item->getName(),
                            'currency' => $items[0]->getCurrency(),
                            'timezone' => $items[0]->getTimezone()
                        ]);
                    }
                }
            }
        }
    }
}
