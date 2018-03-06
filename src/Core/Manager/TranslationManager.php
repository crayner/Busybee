<?php
namespace App\Core\Manager;

use App\Entity\Translate;
use App\Repository\TranslateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationManager implements TranslatorInterface, TranslatorBagInterface
{
    /**
     * @var array
     * Ensure that your add an entry in the translation home.en.yaml file under school.search.replace for each new entry.
     */
    private $source = [
        'this_school' => 'this_school',
        'form_grade' => 'form_grade',
        'forms_grades' => 'forms_grades',
        'unique' => 'unique',
    ];

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        $trans = $this->translator->trans($id, $parameters, $domain, $locale);

        return $this->getInstituteTranslation($trans, $locale);
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param int         $number     The number to use to find the indice of the message
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * Sets the current locale.
     *
     * @param string $locale The locale
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    public function setLocale($locale)
    {
        return $this->translator->setLocale($locale);
    }

    /**
     * Returns the current locale.
     *
     * @return string The locale
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * Gets the catalogue by locale.
     *
     * @param string|null $locale The locale or null to use the default
     *
     * @return MessageCatalogueInterface
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    public function getCatalogue($locale = null)
    {
        return $this->translator->getCatalogue($locale);
    }

    /**
     * @param $trans
     * @return string
     */
    private function getInstituteTranslation($trans, $locale): string
    {
        $matches = [];
        preg_match_all('/!!!(.*?)!!!/', $trans, $matches);

        if (empty($matches[1]))
            return $trans;

        foreach($matches[1] as $q=>$source)
        {
            $translate = null;
            if ($this->settingManager->has($source))
            {
                $translate = new Translate();
                $translate->setValue($this->settingManager->get($source));
            }
            if (empty($translate) && ! empty($locale)) // translate override
                $translate = $this->translateRepository->findOneBy(['source' => $source, 'locale' => $locale]);

            if (empty($translate) && ! empty($this->getLocale())) // system locale
                $translate = $this->translateRepository->findOneBy(['source' => $source, 'locale' => $this->getLocale()]);

            if (empty($translate) && ! empty($this->settingManager->getParameter('locale')))  // fallBack Locale
                $translate = $this->translateRepository->findOneBy(['source' => $source, 'locale' => $this->settingManager->getParameter('locale')]);

            if (empty($translate))
            {
                if (in_array($source, $this->source))
                {
                    $translate = new Translate();
                    $translate->setValue($this->trans('school.search.replace.' . $source, [], 'home'));
                    $translate->setSource( $source );
                    $translate->setLocale($this->settingManager->getParameter('locale'));
                    $this->entityManager->persist($translate);
                    $this->entityManager->flush();
                }
            }

            if ($translate instanceof Translate)
                $trans = str_replace($matches[0][$q], $translate->getValue(), $trans);
        }

        return $trans;
    }

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var TranslateRepository
     */
    private $translateRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * TranslationManager constructor.
     * @param TranslatorInterface $translator
     * @param TranslateRepository $translateRepository
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, SettingManager $settingManager)
    {
        $this->translator = $translator;
        $this->translateRepository = $entityManager->getRepository(Translate::class);
        $this->settingManager = $settingManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @var null|Collection
     */
    private $strings;

    /**
     * @return Collection|null
     */
    public function getStrings($refresh = false): ?Collection
    {
        if (empty($this->strings) || $refresh)
            $this->strings = new ArrayCollection($this->entityManager->getRepository(Translate::class)->findBy([],['source' => 'ASC', 'locale' => 'ASC']));

        return $this->strings;
    }

    /**
     * @param Collection|null $strings
     * @return TranslationManager
     */
    public function setStrings(?Collection $strings): TranslationManager
    {
        if (empty($strings))
            $strings = new ArrayCollection();

        $this->strings = $strings;

        return $this;
    }

    /**
     * @param Translate|null $translate
     * @return TranslationManager
     */
    public function addString(?Translate $translate): TranslationManager
    {
        if (empty($translate) || ! $translate instanceof Translate)
            return $this;

        if ($this->getStrings()->contains($translate))
            return $this;

        $this->strings->add($translate);

        return $this;
    }

    /**
     * @param Translate|null $translate
     * @return TranslationManager
     */
    public function removeString(?Translate $translate): TranslationManager
    {
        $this->getStrings()->removeElement($translate);

        return $this;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}