<?php declare(strict_types=1);

namespace Aggrosoft\LoadCmsBlocks\Twig;

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
        private readonly SalesChannelCmsPageLoaderInterface $cmsPageLoader,
        private readonly RequestStack $requestStack,
        private readonly HttpKernelInterface $kernel,
    )
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('cms', [$this, 'renderCms']),
        ];
    }

    public function renderCms(string $id, SalesChannelContext $context)
    {
        if ($this->requestStack->getParentRequest()){
            // infinite loop protection
            return '';
        }

        $request = $this->requestStack->getCurrentRequest();
        $request->attributes->set('_route', 'frontend.cms.page');
        $request->attributes->set('_controller', 'Shopware\Storefront\Controller\CmsController::page');
        $request->attributes->set('id', $id);
        $response = $this->kernel->handle($request, HttpKernelInterface::SUB_REQUEST);
        return $response->getContent();
    }
}