<?php
/**
 * See lib/base in this plugin for the actual code. You can extend that
 * class in your own application level override of this file
 * @package    Apostrophe
 * @author     P'unk Avenue <apostrophe@punkave.com>
 */
class BaseaHTML5MediaActions extends BaseaMediaActions
{
  public function executeHtml5Upload(sfWebRequest $request)
  {
    // Belongs at the beginning, not the end
    $this->forward404Unless(aMediaTools::userHasUploadPrivilege());

    if ($request->hasParameter('qqfile'))
    {
      // Handle uploaded file
      $allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');
      $sizeLimit = min(array($this->toBytes(ini_get('post_max_size')), $this->toBytes(ini_get('upload_max_filesize')))) - 1024;      // max file size in bytes
      $mediaDir = sfConfig::get('sf_upload_dir') . '/html5upload' . aGuid::generate();

      mkdir($mediaDir);
      $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

      $result = $uploader->handleUpload($mediaDir . '/');
      $success = isset($result['success'])? $result['success'] : false;

      $result = json_encode($result);

      if ($success)
      {
        $importer = new aMediaImporter(array('dir' => $mediaDir, 'feedback' => array($this, 'importFeedback')));
        $importer->go();

        /**
         * Remove everything including files the media importer turned up its nose at
         */
        aFiles::rmRf($mediaDir);
      }

      return $this->renderText($result);
    }
  }

  private function toBytes($str){
    $val = trim($str);
    $last = strtolower($str[strlen($str)-1]);
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
  }

  /**
   * Must be public to be part of a callable
   *
   * NOTE: Copied directly from the task
   *
   * @param mixed $category
   * @param mixed $message
   * @param mixed $file
   */
  public function importFeedback($category, $message, $file = null)
  {
    if (($category === 'total') || ($category === 'info') || ($category === 'completed'))
    {
      if ($this->verbose)
      {
        if (($category === 'total') || ($category === 'completed'))
        {
          echo((is_null($file) ? '' : $file . ": ") . "Files converted: $message\n");
        }
        else
        {
          echo((is_null($file) ? '' : $file . ": ") . "$message\n");
        }
      }
    }
    if ($category === 'error')
    {
      echo((is_null($file) ? '' : $file . ": ") . "$message\n");
    }
  }
}

