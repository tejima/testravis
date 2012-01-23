<?php

class defaultActions extends opApiActions
{
  public function executeError400(sfWebRequest $request)
  {
    $this->getResponse()->setStatusCode(400);
    $response = array('error' => 'Bad Request');

    if (isset($request['error_message']))
    {
      $response['message'] = $request['error_message'];
    }

    return $this->renderJSON($response);
  }

  public function executeError401(sfWebRequest $request)
  {
    $this->getResponse()->setStatusCode(401);
    $response = array('error' => 'Unauthorized');

    if (isset($request['error_message']))
    {
      $response['message'] = $request['error_message'];
    }

    return $this->renderJSON($response);
  }

  public function executeError403(sfWebRequest $request)
  {
    $this->getResponse()->setStatusCode(403);
    $response = array('error' => 'Forbidden');

    if (isset($request['error_message']))
    {
      $response['message'] = $request['error_message'];
    }

    return $this->renderJSON($response);
  }

  public function executeError404(sfWebRequest $request)
  {
    $this->getResponse()->setStatusCode(404);
    $response = array('error' => 'Not Found');

    if (isset($request['error_message']))
    {
      $response['message'] = $request['error_message'];
    }

    return $this->renderJSON($response);
  }
}
