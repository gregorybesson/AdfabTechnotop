<?php
namespace Application\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class StatisticsController extends AbstractActionController
{
    /**
     * @var gameService
     */
    protected $gameService;

    /**
     * @var pageService
     */
    protected $pageService;

    /**
     * @var gameService
     */
    protected $rewardService;

    /**
     * @var achievementService
     */
    protected $achievementService;

    /**
     * @var userService
     */
    protected $userService;

    /**
     * @var achievementListenerService
     */
    protected $achievementListenerService;

    public function indexAction()
    {
        return new ViewModel();
    }

    public function downloadAction()
    {
        $su 			= $this->getUserService();
        $sg 			= $this->getGameService();
        $sr 			= $this->getRewardService();

        $allUser 		= count($su->findAll());
        $activeUser 	= count($su->findByState(1));
        $inactiveUser 	= $allUser - $activeUser;

        $maleUser 		= count($su->findByTitle('M'));
        $femaleUser 	= count($su->findByTitle('Me'));

        $optinUser 		= count($su->findByOptin(1));

        $activeGame 	= count($sg->getActiveGames());

        $allGames 		= count($sg->findAll());
        $allEntries 	= count($sg->findAllEntry());
        $userPerGames 	= $allEntries/$allGames;

        $activePage 	= count($this->getPageService()->getActivePages());

        $shareAction 	= count($sr->findBy(array('actionId' => array(13,14,15,16))));
        $sharePerGames 	= $allGames/$shareAction;

        $userPerBadges = array();
        $badges 	= $this->getRewardAchievementListenerService()->getBadges();

        foreach ($badges as $keyBadge => $badge) {
            foreach ($badge['levels'] as $keyLevel => $level) {
                $userPerBadges[] = array(
                    'badge' => $keyBadge,
                    'level' => $level['label'],
                    'user'  => count($this->getAchievementService()->findBy(array('type' => 'badge', 'category'=> $keyBadge, 'level' => $keyLevel))),
                );

            }
        }

        $content        = "\xEF\xBB\xBF"; // UTF-8 BOM
        $content       .= "Inscrits Actifs;Inscrits Suspendus;Inscrits Homme;Inscrits Femme;Abonnement Newsletter Playground;Jeux Actifs;Articles Actifs;Participant par jeu;Partages par jeu;";

        foreach ($userPerBadges as $badge) {
            $content .=  $badge['badge'].' '.$badge['level'].';';
        }

        $content       .= "\n";

        $content   .= $activeUser
        . ";" . $inactiveUser
        . ";" . $maleUser
        . ";" . $femaleUser
        . ";" . $optinUser
        . ";" . $activeGame
        . ";" . $activePage
        . ";" . number_format($userPerGames, 2)
        . ";" . number_format($sharePerGames, 2);

        foreach ($userPerBadges as $badge) {
            $content .=  $badge['user'].';';
        }

        $content       .= "\n";

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Encoding: UTF-8');
        $headers->addHeaderLine('Content-Type', 'text/csv; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', "attachment; filename=\"statistics.csv\"");
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($content));

        $response->setContent($content);

        return $response;
    }

    public function getAchievementService()
    {
        if (!$this->achievementService) {
            $this->achievementService = $this->getServiceLocator()->get('playgroundreward_achievement_service');
        }

        return $this->achievementService;
    }

    public function setAchievementService($achievementService)
    {
        $this->achievementService = $achievementService;

        return $this;
    }

    public function getRewardService()
    {
        if (!$this->rewardService) {
            $this->rewardService = $this->getServiceLocator()->get('playgroundreward_event_service');
        }

        return $this->rewardService;
    }

    public function setRewardService(GameService $rewardService)
    {
        $this->rewardService = $rewardService;

        return $this;
    }

    public function getRewardAchievementListenerService()
    {
        if (!$this->achievementListenerService) {
            $this->achievementListenerService = $this->getServiceLocator()->get('playgroundreward_achievement_listener');
        }

        return $this->achievementListenerService;
    }

    public function setRewardAchievementListenerService(GameService $achievementListenerService)
    {
        $this->achievementListenerService = $achievementListenerService;

        return $this;
    }

    public function getGameService()
    {
        if (!$this->gameService) {
            $this->gameService = $this->getServiceLocator()->get('playgroundgame_game_service');
        }

        return $this->gameService;
    }

    public function setGameService(GameService $gameService)
    {
        $this->gameService = $gameService;

        return $this;
    }

    public function getPageService()
    {
        if (!$this->pageService) {
            $this->pageService = $this->getServiceLocator()->get('playgroundcms_page_service');
        }

        return $this->pageService;
    }

    public function setPageService(\PlaygroundCms\Service\Page $pageService)
    {
        $this->pageService = $pageService;

        return $this;
    }

    public function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('playgrounduser_user_service');
        }

        return $this->userService;
    }

    public function setUserService($userService)
    {
        $this->userService = $userService;

        return $this;
    }

}
