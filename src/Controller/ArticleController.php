<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;

class ArticleController extends Controller
{
	/**
	 * Get a single article
	 * @param  string $id 
	 */
	public function get(string $id) 
	{
		
		$objArticle = $this->getDoctrine()->getRepository(Article::class)->find($id);

		return new JsonResponse($objArticle, Response::HTTP_CREATED);
	}


	/**
	 * Edit an existing article
	 * @param  string  $id      if of the article
	 * @param  Request $request 
	 */
	public function post(string $id, Request $request) 
	{
    	$strTitle = $request->get('title');
    	$strContent = $request->get('content');
    	$strAuthor = $request->get('author');
    	$strThumbnail = $request->get('thumbnail');

    	$objArticle = $this->getDoctrine()->getRepository(Article::class)->find($id);

    	if(!empty($strTitle) && $objArticle->getTitle() != $strTitle) {
        	$objArticle->setTitle($strTitle);
    	}
    	if(!empty($strContent) && $objArticle->getContent() != $strContent) {
    		$objArticle->setContent($strContent);
    	}
    	if(!empty($strAuthor) && $objArticle->getAuthor() != $strAuthor) {
    		$objArticle->setAuthor($strAuthor);
    	}
    	if(!empty($strThumbnail) && $objArticle->getThumbnail() != $strThumbnail) {
    		$objArticle->setThumbnail($strThumbnail);
    	} 
        
        $objArticle->setLastModified(new \Datetime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($objArticle);
        $entityManager->flush();

		return new JsonResponse($objArticle, Response::HTTP_OK);
	}

	/**
	 * Create a new article
	 * @param  Request $request 
	 */
	public function put(Request $request) 
	{

    	$strTitle = $request->get('title');
    	$strContent = $request->get('content');
    	$strAuthor = $request->get('author');
    	$strThumbnail = $request->get('thumbnail');

    	if(empty($strTitle)) {
    		return new JsonResponse('field title is empty', Response::HTTP_BAD_REQUEST );
    	}
    	if(empty($strContent)) {
    		return new JsonResponse('field content is empty', Response::HTTP_BAD_REQUEST );
    	}
    	if(empty($strAuthor)) {
    		return new JsonResponse('field author is empty', Response::HTTP_BAD_REQUEST );
    	}
    	if(empty($strThumbnail)) {
    		return new JsonResponse('field thumbnail is empty', Response::HTTP_BAD_REQUEST );
    	}

        $objArticle = new Article();
        $objArticle->setTitle($strTitle);
        $objArticle->setContent($strContent);
        $objArticle->setAuthor($strAuthor);
        $objArticle->setThumbnail($strThumbnail);
        $objArticle->setLastModified(new \Datetime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($objArticle);
        $entityManager->flush();

		return new JsonResponse($objArticle, Response::HTTP_CREATED);
	}

	/**
	 * Get the list of articles from an optionnal offset and an optionnal limit 
	 * @param  Request $request 
	 */
	public function getArticles(Request $request) 
	{

		$intOffset = $request->get('offset') ?? 0;
		$intLimit = $request->get('limit') ?? 20;

		$arrResult = $this->getDoctrine()->getRepository(Article::class)->getArticles(
			$intOffset,
			$intLimit
		);

        return new JsonResponse($arrResult, Response::HTTP_OK);
	}
}