<?php

// src/AppBundle/Menu/MenuBuilder.php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Reprository\Category;

class MenuBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    private $em;
    private $factory;
    
    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, EntityManagerInterface $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }


    public function createMainMenu(RequestStack $requestStack )
    {
        $menu = $this->factory->createItem('root', array(
            'childrenAttributes' => array(
            'class' => 'nav-menu nav navbar-nav',
          ),
        ));
        
        $menu->addChild('Home', ['route' => 'front']);

        foreach ($this->em->getRepository('App\Entity\Category')->findAll() as $item) {
          $menu->addChild($item->getTitle(), [
              'route' => 'category',
              'routeParameters' => ['category' => $item->getId()]
          ]);
        }
        
        $menu->addChild('Editor', ['route' => 'editor']);

        return $menu;
    }
}
