# Usage:

```php
public function search(Request $request, SearchInterface $searh)
{
    $q = $request->query->get('q', '');

    $result = $search->search(SearchConstants::PRODUCTS_INDEX, $q);

    ProductController.php on line 60:
    App\Infrastructure\Search\SearchResult {#776 ▼
    -items: array:14 [▼
        0 => App\Infrastructure\Search\Model\Product {#798 ▼
        -id: "14"
        -name: "Name"
        -price: 120.0
        -description: "sdfsdf"
        -rating: 0.0
        -createdAt: 1624459789
        -image: "Imajopzkerf"
        -store: App\Infrastructure\Search\Model\Store {#797 ▶}
        }
        1 => App\Infrastructure\Search\Model\Product {#867 ▶}
        2 => App\Infrastructure\Search\Model\Product {#869 ▶}
        3 => App\Infrastructure\Search\Model\Product {#876 ▶}
        4 => App\Infrastructure\Search\Model\Product {#877 ▶}
        5 => App\Infrastructure\Search\Model\Product {#879 ▶}
        6 => App\Infrastructure\Search\Model\Product {#881 ▶}
        7 => App\Infrastructure\Search\Model\Product {#883 ▶}
        8 => App\Infrastructure\Search\Model\Product {#885 ▶}
        9 => App\Infrastructure\Search\Model\Product {#887 ▶}
        10 => App\Infrastructure\Search\Model\Product {#889 ▶}
        11 => App\Infrastructure\Search\Model\Product {#891 ▶}
        12 => App\Infrastructure\Search\Model\Product {#893 ▶}
        13 => App\Infrastructure\Search\Model\Product {#895 ▶}
    ]
    -total: 14
    }
}

```


# Developer Review
```php
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MultiMatch;

$match = new MultiMatch();
$match->setQuery($query);
$match->setFields(["title^4", "summary", "content", "author"]);

$bool = new BoolQuery();
$bool->addMust($match);

$elasticaQuery = new Query($bool);

$elasticaQuery->setSize($limit);
$foundPosts = $client->getIndex('blog')->search($elasticaQuery);
$results = [];
foreach ($foundPosts as $post) {
    $results[] = $post->getSource();
}
```

```php
// default to localhost:9200, no config!
$elasticaClient = new \Elastica\Client();

/** @var $festivalIndex \Elastica\Index */
$festivalIndex = $elasticaClient->getIndex('festival');

$festivalIndex->exists(); // => false

$festivalIndex->create(
    array(
        'number_of_shards' => 1,
        'number_of_replicas' => 0,
        // Analysis here too
    ),
    true // delete index if it already exists
);

/** @var $hellfestType \Elastica\Type */
$hellfestType = $festivalIndex->getType('hellfest');

$gigs = json_decode(file_get_contents("data-merged.json"), true);

// Slow
foreach ($gigs as $gig) {

    $hellfestType->addDocument(
        new \Elastica\Document('', $gig)
    );

}

// Bulk Indexing

$docs = array();

foreach ($gigs as $gig) {
    $docs[] = new \Elastica\Document('', $gig);
}

// Only one request!
$hellfestType->addDocuments($docs);

```

# Search

```bash
GET festival/hellfest/_search
```

 ```json
{
    "query": {
        "match": {
            "name": "slayer"
        }
    }
}
```

```bash
GET INDEX/TYPE/_search
```
```json
{
    "query": {
        "TYPE DE RECHERCHE": {
            "PARAMÉTRES"
        }
    }
}
```

```php
$matchQuery = new \Elastica\Query\Match();
$matchQuery->setField('name', 'Slayer');

// Not mandatory
$searchQuery = new \Elastica\Query();
$searchQuery->setQuery($matchQuery);

$resultSet = $hellfestType->search($matchQuery);
```

```php
GET festival/hellfest/_search // getType()->search()

{
    "query": { // new \Elastica\Query()
        "match": { // new \Elastica\Query\Match()
            "name": "slayer" // setField()
        }
    }
}
```
### Bool Query

```php
{
    "bool": {
        "must": [
            // Queries to match
        ],
        "must_not": [
            // Queries to not match
        ]
    }
}
```

```php
$boolQuery = new \Elastica\Query\Bool();

$boolQuery->addMust($progressiv);
$boolQuery->addMust($year);
$boolQuery->addMustNot($grindcore);

$resultSet = $hellfestMappingType->search($boolQuery);
```

```php
GET festival/hellfest/_search
{
    "size": 0,
    "aggs": {
        "by_year": {
            "terms": {
                "field": "year",
                "size": 50
            }
        }
    }
}
SELECT COUNT(*) FROM gigs GROUP BY year

$yearsAgg = new \Elastica\Aggregation\Terms("by_year");
$yearsAgg->setSize(50);
$yearsAgg->setField('year');

$search = new \Elastica\Query();
$search->setSize(0);
$search->addAggregation($yearsAgg);

$results = $type->search($search);
$years   = $results->getAggregation('by_year');
```

### convertRequestToCurlCommand
```php
use Elastica\Util;

echo Util::convertRequestToCurlCommand(
    $type->getIndex()->getClient()->getLastRequest()
);
curl -XGET 'http://host:9200/index/type/_search' -d '{"query":{"query_string":{"query":"coucou"}}}'
```

### A lotof Mach

```php
$matching = new \Elastica\Query\Bool();
$matchQuery = new \Elastica\Query\Match();
$matchQuery->setField('name', $terms);
$mathing->addShould($matchQuery);

$matchQuery = new \Elastica\Query\Match();
$matchQuery->setField('pays', $terms);
$mathing->addShould($matchQuery);

$matchQuery = new \Elastica\Query\Match();
$matchQuery->setField('bio', $terms);
$mathing->addShould($matchQuery);

$matchQuery = new \Elastica\Query\Match();
$matchQuery->setField('job', $terms);
$mathing->addShould($matchQuery);


# Use One MultiMatch

$matching = new Query\MultiMatch();
$matching->setQuery($terms);
$matching->setParam('use_dis_max', false);
$matching->setFields(array(
    'name',
    'bio',
    'pays',
    'job',
));
```

### Use JSON

```php
$query = '{"query": {"match_all": {}}}';

$path = $index->getName() . '/' . $type->getName() . '/_search';

$response = $client->request($path, Request::GET, $query);
$responseArray = $response->getData();
```

### Range
```php
$rangeFilter = new Query\Range();
$rangeFilter->addField('field', ['gte' => $min, 'lte' => $max]);
```

# Examples of Searching

```json
DELETE store


# Create an index(database) called store
PUT /store
{
  "mappings": {
    "properties": {
      "name": {"type": "text"},
      "desc": {"type": "text"}, 
      "products": {"type": "integer"},
      "createdAt": {"type": "date"},
      "location": {"type": "geo_point"},
      "rating": {"type": "rank_feature"},
      "url": {"type": "text", "index": false},
      "category":{
      "type": "object",
      "properties":{
        "name": {"type": "text"},
        "id": {"type": "integer"}
      }
    }
    }
  }
}


POST store/_mapping
{
  "properties":{
    "category":{
      "type": "object",
      "property":{
        "name": {"type": "text"},
        "id": {"type": "integer"}
      }
    }
  }
}


#Add some documents
POST store/_doc
{
  "name": "pelestine",
  "location": {"lat": 31.046051	, "lon": 34.851612}, 
  "products": 112, 
  "createdAt": "2011-02-14",
  "desc": "pelestine Description store",
  "rating": 12,
  "url": "https://isis.com",
  "category":{
    "name": "ecom",
    "id": 1
  }
}

# Search for a document with the name is "India"
GET store/_search
{
  "query": {
    "match": {
      "name": "India"
    }
  }
}

# Get Reports about the products
GET store/_search
{
  "aggs": {
    "stats_store": {
      "stats": {
        "field": "products"
      }
    }
  },
  "size": 0
}

# Search india in the fields name and desc
GET store/_search
{
  "query": {
    "multi_match": {
      "query": "https",
      "fields": ["name", "desc"]
    }
  }
}

# Get stores with number of product > 10, and the stors with heigh rating should be first 
GET store/_search
{
  "query": {
    "bool": {
      "must": [
        {
          "range": {
            "products": {
              "gte": 10
            }
          }
        }
      ],
      "should": [
        {
          "rank_feature": {
            "field": "rating"
          }
        }
      ]
    }
  },
  "_source": ["name", "createdAt"]
}


# Boosts the relevance score of documents closer to a provided origin date or point. For example, you can use this query to give more weight to documents closer to a certain date or location.
# pivot : Distance from the origin at which relevance scores receive half of the boost value.
GET store/_search
{
 "query": {
   "distance_feature": {
     "field": "location",
     "origin": {"lat" : 31.791702, "lon" : -7.09262},
     "pivot": "100km"
   }
 },
 "_source": "location"
}

# Search In category

GET store/_search
{
  "query": {
    "bool": {
      "must": [
        {
         "match": {
           "name": "france"
         }
        },
        {
          "constant_score": {
            "filter": {
              "term": {
                "category.name":"sport"
              }
            }
          }
        }
      ]
    }
  },
  "_source": ["name", "category.name"]
}
```