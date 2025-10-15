<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/14/13
 * Time: 1:02 PM
 */
namespace SiteMap\Model;
class Url
{

    const ALWAYS = 'always';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';
    const NEVER = 'never';

    /**
     * @var string Url to the page
     */
    public $location;
    /**
     * @var int time() of last modification
     */
    public $lastModified;
    /**
     * @var string how often this page changes
     */
    public $changeFrequency;
    /**
     * @var float between 0.0 to 1.0
     */
    public $priority;

    /**
     * @param string $location Url
     * @param int $lastModified time()
     * @param string $changeFrequency
     * @param float $priority 0.0 to 1.0
     */
    public function __construct($location, $priority = null, $lastModified = null, $changeFrequency = Url::NEVER)
    {
        $this->location = $location;
        $this->lastModified = $lastModified;
        $this->changeFrequency = $changeFrequency;
        $this->priority = $priority;
    }

    public function toXml()
    {
        $location = "<loc>%s</loc>";
        $lastModified = "<lastmod>%s</lastmod>";
        $changeFrequency = "<changefreq>%s</changefreq>";
        $priority = "<priority>5s</priority>";
        $url = "<url>%s</url>";

        $xml = array();

        //rawurlencode
        $location = sprintf($location, ($this->location));
        $xml[] = $location;

        if ($this->lastModified) {
            $lastModified = sprintf($lastModified, date("Y-m-d", $this->lastModified));
            $xml[] = $lastModified;
        }

        if ($this->priority) {
            $priority = sprintf($priority, $this->priority);
            $xml[] = $priority;
        }

        if ($this->changeFrequency) {
            $changeFrequency = sprintf($changeFrequency, $this->changeFrequency);
            $xml[] = $changeFrequency;
        }

        return sprintf($url, implode("\n", $xml));
    }
} 