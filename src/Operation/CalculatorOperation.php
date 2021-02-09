<?php

namespace Measoft\Operation;

use Measoft\MeasoftException;
use Measoft\Object\CalculationResult;

class CalculatorOperation extends AbstractOperation
{
	/**
	 * @var string $townFrom Город-отправитель
	 */
	private $townFrom;

    /**
     * @var string $addressFrom Адрес в городе-отправителе
     */
	private $addressFrom;

	/**
	 * @var string $townTo Город-получатель
	 */
	private $townTo;

    /**
     * @var string $addressTo Адрес в городе-получателе
     */
	private $addressTo;

    /**
     * @var string $zipCode Почтовый индекс в городе-получателе
     */
	private $zipCode;

    /**
     * @var string $pvz Код пункта самовывоза по справочнику
     */
	private $pvz;

	/**
	 * @var float $length Длина в сантиметрах
	 */
	private $length;

	/**
	 * @var float $width Ширина в сантиметрах
	 */
	private $width;

	/**
	 * @var float $height Высота в сантиметрах
	 */
	private $height;

	/**
	 * @var float $weight Масса в килограммах
	 */
	private $weight;

	/**
	 * @var int $service Режим доставки
	 */
	private $service;

    /**
     * @var float $price Сумма наложенного платежа
     */
	private $price;

    /**
     * @var float $insurancePrice Сумма объявленной ценности
     */
	private $insurancePrice;

	/**
	 * @param string $townFrom Город-отправитель
	 * @return self
	 */ 
	public function setTownFrom(string $townFrom): self
	{
		$this->townFrom = $townFrom;

		return $this;
	}

	/**
	 * @param string $townTo Город-получатель
	 * @return self
	 */ 
	public function setTownTo(string $townTo): self
	{
		$this->townTo = $townTo;

		return $this;
	}

	/**
	 * @param float $length Длина в сантиметрах
	 * @return self
	 */ 
	public function setLength(float $length): self
	{
		$this->length = $length;

		return $this;
	}

	/**
	 * @param float $width Ширина в сантиметрах
	 * @return self
	 */ 
	public function setWidth(float $width): self
	{
		$this->width = $width;

		return $this;
	}

	/**
	 * @param float $height Высота в сантиметрах
	 * @return self
	 */ 
	public function setHeight(float $height): self
	{
		$this->height = $height;

		return $this;
	}

	/**
	 * @param float $weight Масса в килограммах
	 * @return self
	 */ 
	public function setWeight(float $weight): self
	{
		$this->weight = $weight;

		return $this;
	}

	/**
	 * @param int $service Режим доставки
	 * @return self
	 */ 
	public function setService(int $service): self
	{
		$this->service = $service;

		return $this;
	}

    /**
     * @param string $addressFrom
     * @return CalculatorOperation
     */
    public function setAddressFrom(string $addressFrom): self
    {
        $this->addressFrom = $addressFrom;
        return $this;
    }

    /**
     * @param string $addressTo
     * @return CalculatorOperation
     */
    public function setAddressTo(string $addressTo): self
    {
        $this->addressTo = $addressTo;
        return $this;
    }

    /**
     * @param string $zipCode
     * @return CalculatorOperation
     */
    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * @param string $pvz
     * @return CalculatorOperation
     */
    public function setPvz(string $pvz): self
    {
        $this->pvz = $pvz;
        return $this;
    }

    /**
     * @param float $price
     * @return CalculatorOperation
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @param float $insurancePrice
     * @return CalculatorOperation
     */
    public function setInsurancePrice(float $insurancePrice): self
    {
        $this->insurancePrice = $insurancePrice;
        return $this;
    }

	/**
	 * Сформировать XML
	 *
	 * @return \SimpleXMLElement
	 */
	private function buildXml(): \SimpleXMLElement
	{
		$xml  = $this->createXml('calculator');
		$calc = $xml->addChild('calc');
		
		$calc->addAttribute('townfrom', $this->townFrom);
		if ($this->addressFrom) {
            $calc->addAttribute('addressfrom', $this->addressFrom);
        }
		$calc->addAttribute('townto', $this->townTo);
        if ($this->addressFrom) {
            $calc->addAttribute('addressto', $this->addressTo);
        }
        if ($this->pvz) {
            $calc->addAttribute('pvz', $this->pvz);
        }
        if ($this->zipCode) {
            $calc->addAttribute('zipcode', $this->zipCode);
        }
		$calc->addAttribute('l', $this->length);
		$calc->addAttribute('w', $this->width);
		$calc->addAttribute('h', $this->height);
		$calc->addAttribute('mass', $this->weight);
		$calc->addAttribute('service', $this->service);
        if ($this->price !== null) {
            $calc->addAttribute('prcie', $this->price);
        }
        if ($this->insurancePrice !== null) {
            $calc->addAttribute('inshprice', $this->insurancePrice);
        }

		return $xml;
	}

    /**
     * Расчет стоимости доставки
     *
     * @return CalculationResult[]
     * @throws MeasoftException
     */
	public function calculate(): array
	{
		$response = $this->request($this->buildXml());

		if (!$response->isSuccess()) {
			throw new MeasoftException($response->getError());
		}

		$resultXml = $response->getXml();

		foreach ($resultXml as $item) {
			$result[] = CalculationResult::getFromXml($item);
		}

		return $result ?? [];
	}
}