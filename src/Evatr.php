<?php

namespace Codedge\Evatr;

use PhpXmlRpc\Client;
use PhpXmlRpc\Request;
use PhpXmlRpc\Response;
use PhpXmlRpc\Value;

class Evatr extends AbstractEvatr
{
    /**
     * Evatr connection url for the XML RPC.
     */
    const EVATRINTERFACEURL = 'https://evatr.bff-online.de';

    /**
     * @var string
     */
    private $ownUstId = '';

    /**
     * @var string
     */
    private $foreignUstId = '';

    /**
     * @var string
     */
    private $companyName = '';

    /**
     * @var string
     */
    private $street = '';

    /**
     * @var string
     */
    private $city = '';

    /**
     * @var string
     */
    private $zipCode = '';

    /**
     * @var bool
     */
    private $printConfirmation = false;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var EvatrXmlResponse
     */
    private $xmlResponse;

    /**
     * Evatr constructor.
     */
    public function __construct()
    {
        $this->client = new Client(self::EVATRINTERFACEURL);
    }

    /**
     * Set your own (German) VAT ID.
     *
     * @param string $ustId
     *
     * @return $this
     */
    public function setOwnUstId($ustId)
    {
        $this->ownUstId = $ustId;

        return $this;
    }

    /**
     * Set the foreign VAT ID. This is just an alias for setOwnUstId().
     *
     * @param string $ustId
     *
     * @return $this
     */
    public function setUstId1($ustId)
    {
        $this->setOwnUstId($ustId);

        return $this;
    }

    /**
     * Set the European / foreign VAT ID that you want to check.
     *
     * @param string $ustId
     *
     * @return $this
     */
    public function setForeignUstId($ustId)
    {
        $this->foreignUstId = $ustId;

        return $this;
    }

    /**
     * Set the foreign VAT ID. This is just an alias for setForeignUstId().
     *
     * @param $ustId
     *
     * @return $this
     */
    public function setUstId2($ustId)
    {
        $this->setForeignUstId($ustId);

        return $this;
    }

    /**
     * Set the company name.
     *
     * @param string $companyName
     *
     * @return $this
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Set the company street.
     *
     * @param $street
     *
     * @return $this
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Set the companys zip code.
     *
     * @param string $zipCode
     *
     * @return $this
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Set the companys city.
     *
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Set if an official confirmation is requested.
     *
     * @param bool $option
     *
     * @return $this
     */
    public function setPrintConfirmation(bool $option)
    {
        $this->printConfirmation = $option;

        return $this;
    }

    /**
     * Send the XML RPC query.
     */
    public function query()
    {
        $this->response = $this->client->send(new Request(
            'evatrRPC',
            [
                new Value($this->ownUstId),
                new Value($this->foreignUstId),
                new Value($this->companyName),
                new Value($this->city),
                new Value($this->zipCode),
                new Value($this->street),
                new Value($this->_setPrintConfirmationOption($this->printConfirmation)),
            ]
        ));

        $this->_processResponse();
    }

    /**
     * Get the better formatted XML response.
     *
     * @return EvatrXmlResponse
     */
    public function getResponse()
    {
        return $this->xmlResponse;
    }

    /**
     * Get the plain XML RPC response.
     *
     * @return Response
     */
    public function getPlainResponse()
    {
        return $this->response;
    }

    private function _processResponse()
    {
    	try {
			$r = $this->response;
			$v = $r->value();
			$me = $v->me;
			$this->xmlResponse = new EvatrXmlResponse($me['string']);
		} catch (\Exception $e) {
			throw new \Exception('malformed evatr xml response. possible zope db error. ' . $this->response->value());
		}
    }
}
