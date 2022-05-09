<?php
/**
 * Created by PhpStorm.
 * User: Emanuel
 * Date: 17.09.2019
 * Time: 20:29
 */

namespace App\Form\Type;


use App\Entity\Rooms;
use App\Entity\Server;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Service\ThemeService;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RoomType extends AbstractType
{
    private $parameterBag;
    private $logger;
    private $theme;
    private $translator;

    public function __construct(ParameterBagInterface $parameterBag, LoggerInterface $logger, ThemeService $themeService, TranslatorInterface $translator)
    {
        $this->parameterBag = $parameterBag;
        $this->logger = $logger;
        $this->theme = $themeService;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        if (sizeof($options['server']) !== 1) {
            $builder
                ->add('server', EntityType::class, [
                    'choice_label' => 'serverName',
                    'class' => Server::class,
                    'choices' => $options['server'],
                    'label' => 'label.serverKonferenz',
                    'translation_domain' => 'form',
                    'multiple' => false,
                    'required' => true,
                    'attr' => array('class' => 'moreFeatures')
                ]);
        }

        $builder
            ->add('name', TextType::class, ['required' => false, 'label' => 'label.konferenzName', 'translation_domain' => 'form'])
            ->add('agenda', TextareaType::class, ['required' => false, 'label' => 'label.agenda', 'translation_domain' => 'form'])
            ->add('start', DateTimeType::class, ['required' => false, 'attr' => ['data-minDate' => $options['minDate'], 'class' => 'flatpickr', 'placeholder' => 'placeholder.chooseTime'], 'label' => 'label.start', 'translation_domain' => 'form', 'widget' => 'single_text'])
            ->add('duration', ChoiceType::class, [
                'label' => 'label.dauerKonferenz',
                'translation_domain' => 'form',
                'choices' => [
                    'option.15min' => 15,
                    'option.30min' => 30,
                    'option.45min' => 45,
                    'option.60min' => 60,
                    'option.75min' => 75,
                    'option.90min' => 90,
                    'option.105min' => 105,
                    'option.120min' => 120,
                    'option.150min' => 150,
                    'option.180min' => 180,
                    'option.210min' => 210,
                    'option.240min' => 240,
                    'option.270min' => 270,
                    'option.300min' => 300,
                    'option.330min' => 330,
                    'option.360min' => 360,
                    'option.390min' => 390,
                    'option.420min' => 420,
                    'option.450min' => 450,
                    'option.480min' => 480,

                ]
            ])
            ->add('scheduleMeeting', CheckboxType::class, array('required' => false, 'label' => 'label.scheduleMeeting', 'translation_domain' => 'form'));
        if ($this->parameterBag->get('input_settings_persistant_rooms') == 1) {
            $this->logger->debug('Add Persistant Rooms to the Form');
            $builder->add('persistantRoom', CheckboxType::class, array('required' => false, 'label' => 'label.persistantRoom', 'translation_domain' => 'form'));
        };
        if ($this->parameterBag->get('input_settings_only_registered') == 1) {
            $this->logger->debug('Add Only Registered Users to the Form');
            $builder->add('onlyRegisteredUsers', CheckboxType::class, array('required' => false, 'label' => 'label.nurRegistriertenutzer', 'translation_domain' => 'form'));
        };
        if ($this->parameterBag->get('input_settings_share_link') == 1) {
            $this->logger->debug('Add Share Links to the Form');
            $builder->add('public', CheckboxType::class, array('required' => false, 'label' => 'label.puplicRoom', 'translation_domain' => 'form'));
        };

        if ($this->parameterBag->get('input_settings_max_participants') == 1) {
            $this->logger->debug('Add A maximal allowed number of participants to the Form');
            $builder->add('maxParticipants', NumberType::class, array('required' => false, 'label' => 'label.maxParticipants', 'translation_domain' => 'form', 'attr' => array('placeholder' => 'placeholder.maxParticipants')));
        };
        if ($this->parameterBag->get('input_settings_waitinglist') == 1) {
            $this->logger->debug('Add a waitinglist to the Form');
            $builder->add('waitinglist', CheckboxType::class, array('required' => false, 'label' => 'label.waitinglist', 'translation_domain' => 'form'));
        };
        if ($this->parameterBag->get('input_settings_conference_join_page') == 1) {
            $this->logger->debug('Add Show Room on Joinpage to the Form');
            $builder->add('showRoomOnJoinpage', CheckboxType::class, array('required' => false, 'label' => 'label.showRoomOnJoinpage', 'translation_domain' => 'form'));
        };
        if ($this->parameterBag->get('input_settings_deactivate_participantsList') == 1) {
            $this->logger->debug('Add the possibility the users must not be on the participants list  to the Form');
            $builder->add('totalOpenRooms', CheckboxType::class, array('required' => false, 'label' => 'label.totalOpenRooms', 'translation_domain' => 'form'));
        };
        if ($this->parameterBag->get('input_settings_dissallow_screenshare') == 1) {
            $this->logger->debug('Add the possibility to dissallow screenshare');
            $builder->add('dissallowScreenshareGlobal', CheckboxType::class, array('required' => false, 'label' => 'label.dissallowScreenshareGlobal', 'translation_domain' => 'form'));
        }
        if ($this->parameterBag->get('allowTimeZoneSwitch') == 1) {
            $this->logger->debug('Add the possibility to select a Timezone');
            $builder->add('timeZone', TimezoneType::class, array('required' => false, 'label' => 'label.timezone', 'translation_domain' => 'form'));
        }
        if ($this->parameterBag->get('input_settings_allowLobby') == 1) {
            $this->logger->debug('Add the possibility to select the lobby');
            $builder->add('lobby', CheckboxType::class, array('required' => false, 'label' => 'label.lobby', 'translation_domain' => 'form'));
        }
        if ($options['showTag']) {
            $this->logger->debug('Add the possibility to select a tag');
            $builder->add('tag', EntityType::class, array(
                'class' => Tag::class,
                'choice_label' => 'title',
                'query_builder' => function (TagRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.disabled = :false')
                        ->setParameter('false', false)
                        ->orderBy('t.priority', 'ASC');
                },
                'required' => true,
                'label' => 'label.tag',
                'translation_domain' => 'form'
            ));
        }
        $builder->add('submit', SubmitType::class, ['label' => 'label.speichern', 'translation_domain' => 'form', 'attr' => array(
            'class' => 'btn btn-outline-primary')]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'server' => array(),
            'data_class' => Rooms::class,
            'minDate' => 'today',
            'isEdit' => false
        ]);

        $resolver->setDefault('attr', function (Options $options) {
            $attr = array('id' => 'newRoom_form');
            if (!$options['isEdit'] && $this->parameterBag->get('input_settings_allow_edit_tag') == 0 && $this->parameterBag->get('input_settings_allow_tag') == 1) {
                $attr['data-blocktext'] = $this->translator->trans('new.room.blockSave.text');
                return $attr;
            }
            return $attr;
        }
        );
        $resolver->setDefault('showTag', function (Options $options) {
            if ($this->parameterBag->get('input_settings_allow_edit_tag') == 1 && $this->parameterBag->get('input_settings_allow_tag') == 1) {
                return true;
            }
            if (!$options['isEdit'] && $this->parameterBag->get('input_settings_allow_edit_tag') == 0 && $this->parameterBag->get('input_settings_allow_tag') == 1) {
                return true;
            } elseif ($options['isEdit'] && $this->parameterBag->get('input_settings_allow_edit_tag') == 0 && $this->parameterBag->get('input_settings_allow_tag') == 1) {
                return false;
            }
            return false;
        }
        );

    }
}
