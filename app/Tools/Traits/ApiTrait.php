<?php

namespace App\Tools\Traits;

trait ApiTrait {

    /**
     * Returns the API response in pattern
     * @param bool  $success
     * @param array $data
     * @return array
     */
    public function getApiResponse(bool $success, array $data = [])
    {
        $response = ['success' => $success];

        if (!empty($data))
        {
            $response = array_merge($response, ['data' => $data]);
        }

        return $response;
    }
}