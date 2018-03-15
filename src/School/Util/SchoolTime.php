<?php
namespace App\School\Util;

use Symfony\Component\Translation\TranslatorInterface;

class SchoolTime
{
	/**
	 * @var \DateTime
	 */
	private $open;
	/**
	 * @var \DateTime
	 */
	private $begin;
	/**
	 * @var \DateTime
	 */
	private $finish;
	/**
	 * @var \DateTime
	 */
	private $close;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * SchoolTime constructor.
	 *
	 * @param TranslatorInterface $translator
	 */
	public function __construct(TranslatorInterface $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @return \DateTime
	 */
	public function getOpen(): \DateTime
	{
		return $this->open;
	}

	/**
	 * @param \DateTime $open
	 *
	 * @return SchoolTime
	 */
	public function setOpen(\DateTime $open): SchoolTime
	{
		$this->open = $open;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getBegin(): \DateTime
	{
		return $this->begin;
	}

	/**
	 * @param \DateTime $begin
	 *
	 * @return SchoolTime
	 */
	public function setBegin(\DateTime $begin): SchoolTime
	{
		$this->begin = $begin;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getFinish(): \DateTime
	{
		return $this->finish;
	}

	/**
	 * @param \DateTime $finish
	 *
	 * @return SchoolTime
	 */
	public function setFinish(\DateTime $finish): SchoolTime
	{
		$this->finish = $finish;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getClose(): \DateTime
	{
		return $this->close;
	}

	/**
	 * @param \DateTime $close
	 *
	 * @return SchoolTime
	 */
	public function setClose(\DateTime $close): SchoolTime
	{
		$this->close = $close;

		return $this;
	}

	public function getTranslation(string $key): String
	{
		return $this->translator->trans('school_day.time.'.strtolower($key), [], 'Calendar');
	}
}