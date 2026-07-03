<?php

namespace App\Services;

class WorldBankService extends BaseService
{
    protected $baseUrl = 'https://api.worldbank.org/v2';
    protected $cacheDuration = 86400;

    public function getGDP($isoCode, $year = null)
    {
        if (!$year) $year = date('Y') - 1;
        $endpoint = "/country/{$isoCode}/indicator/NY.GDP.MKTP.CD";
        $params = ['format' => 'json', 'date' => $year, 'per_page' => 1];
        $response = $this->get($endpoint, $params);
        
        if ($response && isset($response[1]) && !empty($response[1])) {
            return $response[1][0]['value'] ?? null;
        }
        return null;
    }

    public function getInflation($isoCode, $year = null)
    {
        if (!$year) $year = date('Y') - 1;
        $endpoint = "/country/{$isoCode}/indicator/FP.CPI.TOTL.ZG";
        $params = ['format' => 'json', 'date' => $year, 'per_page' => 1];
        $response = $this->get($endpoint, $params);
        
        if ($response && isset($response[1]) && !empty($response[1])) {
            return $response[1][0]['value'] ?? null;
        }
        return null;
    }

    public function getPopulation($isoCode, $year = null)
    {
        if (!$year) $year = date('Y') - 1;
        $endpoint = "/country/{$isoCode}/indicator/SP.POP.TOTL";
        $params = ['format' => 'json', 'date' => $year, 'per_page' => 1];
        $response = $this->get($endpoint, $params);
        
        if ($response && isset($response[1]) && !empty($response[1])) {
            return $response[1][0]['value'] ?? null;
        }
        return null;
    }

    public function getAllIndicators($isoCode)
    {
        $year = date('Y') - 1;
        return [
            'gdp' => $this->getGDP($isoCode, $year),
            'inflation' => $this->getInflation($isoCode, $year),
            'population' => $this->getPopulation($isoCode, $year),
            'year' => $year
        ];
    }
}