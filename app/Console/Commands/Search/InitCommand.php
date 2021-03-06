<?php

namespace App\Console\Commands\Search;

use App\Query\Shop\Product;
use App\Services\Search\ProductIndexer;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Console\Command;
use Elasticsearch\Client;

class InitCommand extends Command
{
    protected $signature = 'search:init';

    private $client;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle(): bool
    {
        try {
            $this->client->indices()->delete([
                'index' => 'app'
            ]);
        } catch (Missing404Exception $e) {

        }

        $this->client->indices()->create([
            'index' => 'app',
            'body' => [
                'mappings' => [
                    'product' => [
                        '_source' => [
                            'enabled' => true,
                        ],
                        'properties' => [
                            'id' => [
                                'type' => 'integer'
                            ],
                            'category_id' => [
                                'type' => 'integer'
                            ],
                            'brand_id' => [
                                'type' => 'integer'
                            ],
                            'availability' => [
                                'type' => 'string'
                            ],
                            'title' => [
                                'type' => 'string'
                            ],
                            'slug' => [
                                'type' => 'string'
                            ],
                            'price' => [
                                'type' => 'integer'
                            ],
                            'content' => [
                                'type' => 'string'
                            ],
                            'status' => [
                                'type' => 'string'
                            ],
                            'reviews' => [
                                'type' => 'integer'
                            ],
                            'comments' => [
                                'type' => 'integer'
                            ],
                            'rating' => [
                                'type' => 'integer'
                            ],
                            'weight' => [
                                'type' => 'integer'
                            ],
                            'quantity' => [
                                'type' => 'integer'
                            ],
                            'values' => [
                                'type' => 'nested',
                                'properties' => [
                                    'attribute' => [
                                        'type' => 'integer'
                                    ],
                                    'value_string' => [
                                        'type' => 'keyword',
                                    ],
                                    'value_int' => [
                                        'type' => 'integer',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'settings' => [
                    'analysis' => [
                        'char_filter' => [
                            'replace' => [
                                'type' => 'mapping',
                                'mappings' => [
                                    '&=> and '
                                ],
                            ],
                        ],
                        'filter' => [
                            'word_delimiter' => [
                                'type' => 'word_delimiter',
                                'split_on_numerics' => false,
                                'split_on_case_change' => true,
                                'generate_word_parts' => true,
                                'generate_number_parts' => true,
                                'catenate_all' => true,
                                'preserve_original' => true,
                                'catenate_numbers' => true,
                            ],
                            'trigrams' => [
                                'type' => 'ngram',
                                'min_gram' => 4,
                                'max_gram' => 6,
                            ],
                        ],
                        'analyzer' => [
                            'default' => [
                                'type' => 'custom',
                                'char_filter' => [
                                    'html_strip',
                                    'replace',
                                ],
                                'tokenizer' => 'whitespace',
                                'filter' => [
                                    'lowercase',
                                    'word_delimiter',
                                    'trigrams',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return true;
    }
}