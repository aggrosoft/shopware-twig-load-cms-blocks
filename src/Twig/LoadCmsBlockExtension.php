<?php declare(strict_types=1);

namespace Aggrosoft\LoadCmsBlocks\Twig;

use Shopware\Core\Framework\Adapter\Twig\TemplateFinder;
use Shopware\Storefront\Controller\CmsController;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

use Twig\Environment;

class LoadCmsBlockExtension extends AbstractExtension
{
    public function __construct(
        private readonly Environment $twig,
        private readonly TemplateFinder $templateFinder,
        private readonly SalesChannelCmsPageLoaderInterface $cmsPageLoader,
        private readonly RequestStack $requestStack
    )
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('cms', [$this, 'renderCms'], ['needs_context' => true]),
        ];
    }

    public function renderCms(array $context, string $id, SalesChannelContext $salesChannelContext)
    {
        $cmsPage = $this->cmsPageLoader->load($this->requestStack->getCurrentRequest(), new Criteria([$id]), $salesChannelContext)->first();
        $view = $this->templateFinder->find('@Storefront/storefront/page/content/detail.html.twig');
        $result = $this->twig->render($view, array_merge($context, ['cmsPage' => $cmsPage]));
        return $result;
    }
}