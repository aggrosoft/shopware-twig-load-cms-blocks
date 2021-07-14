<?php declare(strict_types=1);

namespace Aggrosoft\LoadCmsBlocks\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\HttpFoundation\RequestStack;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

use Twig\Environment;

class LoadCmsBlockExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var SalesChannelCmsPageLoaderInterface
     */
    private $cmsPageLoader;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(Environment $twig, SalesChannelCmsPageLoaderInterface $cmsPageLoader, RequestStack $requestStack)
    {
        $this->twig = $twig;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('cms', [$this, 'renderCms']),
        ];
    }

    public function renderCms(string $id, SalesChannelContext $context)
    {
        $cmsPage = $this->cmsPageLoader->load($this->requestStack->getCurrentRequest(), new Criteria([$id]), $context)->first();
        $result = $this->twig->render('@Storefront/storefront/page/content/detail.html.twig', ['cmsPage' => $cmsPage]);
        return $result;
    }
}