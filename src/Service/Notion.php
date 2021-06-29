<?php

namespace Underlike\Notion\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Notion
{
    /**
     * Define defaut values for initialization page
     */
    protected $_urlApi = null;
    protected $_data = [
        'properties' => null,
        'children' => null
    ];

    /**
     * Construct function
     * 
     * @param HttpClientInterface $client
     * @param ParameterBagInterface $params
     */
    public function __construct(
        HttpClientInterface $client,
        ParameterBagInterface $params
    ) {
        $this->_client = $client;
        $this->_params = $params;
    }

    /**
     * Set url for API route
     * 
     * @param string $url
     */
    public function setUrlApi($url)
    {
        $this->_urlApi = $url;
        return $this;
    }

    /**
     * Set title of page Notion
     * 
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_data['properties'][] = [
            'Name' => [
                'title' => [
                    [
                        'text' => [
                            'content' => $title
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Set text property for page Notion
     * 
     * @param string $name
     * @param string $value
     */
    public function setText($name, $value)
    {
        $this->_data['properties'][] = [
            $name => [
                'rich_text' => [
                    [
                        'text' => [
                            'content' => $value
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Set number property for page Notion
     * 
     * @param string $name
     * @param int $value
     */
    public function setNumber($name, $value)
    {
        $this->_data['properties'][] = [
            $name => [
                'number' => $value
            ]
        ];
    }

    /**
     * Set select property for page Notion
     * 
     * @param string $name
     * @param string $value
     */
    public function setSelect($name, $value)
    {
        $this->_data['properties'][] = [
            $name => [
                'select' => [
                    'name' => $value
                ]
            ]
        ];
    }

    /**
     * Set url property for page Notion
     * 
     * @param string $name
     * @param string $value
     */
    public function setUrl($name, $value)
    {
        $this->_data['properties'][] = [
            $name => [
                'url' => $value
            ]
        ];
    }

    /**
     * Set email property for page Notion
     * 
     * @param string $name
     * @param string $value
     */
    public function setEmail($name, $value)
    {
        $this->_data['properties'][] = [
            $name => [
                'email' => $value
            ]
        ];
    }

    /**
     * Set checkbox property for page Notion
     * 
     * @param string $name
     * @param bool $value
     */
    public function setCheckbox($name, $value)
    {
        $this->_data['properties'][] = [
            $name => [
                'checkbox' => $value
            ]
        ];
    }

    /**
     * Set multi-select property for page Notion
     * 
     * @param string $name
     * @param array $value
     */
    public function setMultiSelect($name, $value)
    {
        $this->_data['properties'][] = [
            $name => [
                'multi_select' => $value
            ]
        ];
    }

    /**
     * Create page Notion thanks to arguments class
     */
    public function createPage()
    {
        /** Get default configurations */
        $apiUrl = $this->_params->get('api_url');
        $apiVersion = $this->_params->get('api_version');
        $apiKey = $this->_params->get('api_key');
        $apiDatabaseId = $this->_params->get('api_database_id');

        /** Define array properties */
        $properties = [];
        if (is_array($this->_data['properties']) && count($this->_data['properties']) >= 1) {
            foreach ($this->_data['properties'] as $key => $property) {
                $properties[key($property)] = array_values($property)[0];
            }
        }

        /** Execute call */
        $response = $this->_client->request('POST', $apiUrl.$this->_urlApi, [
            'headers' => [
                'Accept' => 'application/json',
                'Notion-Version' => $apiVersion,
                'Authorization' => 'Bearer '.$apiKey
            ],
            'json' => [
                'parent' => [
                    'database_id' => $apiDatabaseId
                ],
                'properties' => $properties,
                'children' => $this->_data['children'] ? $this->_data['children'] : []
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            return true;
        }
        return false;
    }
}