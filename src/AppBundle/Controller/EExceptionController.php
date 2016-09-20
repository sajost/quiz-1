<?php

namespace AppBundle\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Debug\Exception\FlattenException;

/**
 * ExceptionController renders error or exception pages for a given
 * FlattenException.
 *
 */
class EExceptionController extends ExceptionController{
	
	
	public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
	{
		return parent::showAction($request, $exception, $logger);
	}
	
	
	/**
	 * @param Request $request
	 * @param string  $format
	 * @param int     $code          An HTTP response status code
	 * @param bool    $showException
	 *
	 * @return string
	 */
	protected function findTemplate(Request $request, $format="html", $code, $showException)
	{
		$name = $showException ? 'exception' : 'error';
		if ($showException && 'html' == $format) {
			$name = 'exception_full';
		}
	
		// For error pages, try to find a template for the specific HTTP status code and format
		if (!$showException) {
			$template = sprintf('AppBundle:Exception:%s%s.%s.twig', $name, $code, $format);
			if ($this->templateExists($template)) {
				return $template;
			}
		}
	
		// default to a generic HTML exception
		$request->setRequestFormat('html');
	
		return sprintf('AppBundle:Exception:%s.html.twig', $showException ? 'exception_full' : $name);
	}
}
