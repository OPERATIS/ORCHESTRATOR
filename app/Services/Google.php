<?php

namespace App\Services;

class Google
{
    /**
     * @param $analytics
     * @param $accountName
     * @return mixed|null
     */
    public static function getProfileId($analytics, $accountName)
    {
        // Get the list of accounts for the authorized user.
        $accounts = $analytics->management_accounts->listManagementAccounts();

        if (count($accounts->getItems()) > 0) {
            foreach ($accounts->getItems() as $item) {
                if ($item->getName() === $accountName) {
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

                            // Return the first view (profile) ID.
                            return $items[0]->getId();
                        }
                    }
                }
            }
        }

        return null;
    }
}
