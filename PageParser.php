<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpQuery\PhpQuery;

/**
 * A page parser.
 *
 * @property string $url            - The url of needed page for parsing
 * @property PhpQuery $pq             - PhpQuery library
 * @property string $title          - The title of the product
 * @property string $description    - The description of the product
 * @property string $price          - The price of the product
 * @property string $asin           - The ASIN of the product
 * @property array $specifications - The specifications of the product
 * @property array $images         - The images of the product
 */
class PageParser
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var PhpQuery
     */
    private $pq;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $asin;

    /**
     * @var array
     */
    private $specifications;

    /**
     * @var array
     */
    private $images;

    /**
     * Page parser constructor.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Parse HTML content and set values.
     *
     * @return PageParser
     */
    public function parse(): PageParser
    {
        // Parse needed page with PhpQuery library
        $content = file_get_contents($this->url);
        $this->pq = new PhpQuery;
        $this->pq->load_str($content);

        // Set needed values of the product
        $this->title = trim($this->pq->query('#productTitle')[0]->textContent);
        $this->description = trim($this->pq->query('#productDescription p')[0]->textContent);
        $this->price = $this->pq->query('.olp-padding-right .a-color-price')[0]->textContent;

        // Set ASIN and specifications of the product
        $table = $this->pq->query('.techD .content .attrG .pdTab table tbody tr');
        foreach ($table as $item) {
            if ($item->firstChild->textContent === 'ASIN') {
                $this->asin = $item->lastChild->textContent;
                array_pop($this->specifications);
                break;
            }

            $this->specifications[$item->firstChild->textContent] = $item->lastChild->textContent;
        }

        // Set images of the product
        foreach ($this->pq->query('#altImages ul .item') as $ulImgItem) {
            $this->images[] = $ulImgItem->getElementsByTagName('img')[0]->getAttribute('src');
        }

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get ASIN.
     *
     * @return string
     */
    public function getASIN(): string
    {
        return $this->asin;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * Get images.
     *
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * Get specifications.
     *
     * @return array
     */
    public function getSpecifications(): array
    {
        return $this->specifications;
    }

    /**
     * Get all data in JSON format.
     *
     * @return string
     */
    public function getDataInJSON(): string
    {
        $data['title'] = $this->title;
        $data['description'] = $this->description;
        $data['ASIN'] = $this->asin;
        $data['price'] = $this->price;
        $data['specifications'] = $this->specifications;
        $data['images'] = $this->images;

        return json_encode($data);
    }


}
