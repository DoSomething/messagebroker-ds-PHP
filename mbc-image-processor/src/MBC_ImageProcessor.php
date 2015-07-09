<?php
namespace DoSomething\MBC_ImageProcessor;

use DoSomething\StatHat\Client as StatHat;
use DoSomething\MB_Toolbox\MB_Toolbox;
use DoSomething\MB_Toolbox\MB_Toolbox_cURL;

/*
 * MBC_UserAPICampaignActivity.class.in: Used to process the transactionalQueue
 * entries that match the campaign.*.* binding.
 */
class MBC_ImageProcessor extends MBC_ImageProcessingConsumer
{

  /**
   * The image and http path to request.
   */
  protected $imagePath;

  /**
   * Sets values ofr processing based on contents of message from consumed queue.
   *
   * @param array $message
   *  The payload of the message being processed.
   */
  protected function setImagePath($imagePath) {
    $this->imagePath = $imagePath;
  }

  /**
   * Method to process image details. Make requests to trigger image cache processing on the Drupal site.
   */
  protected function process() {

    $result = MB_Toolbox_cURL::curlGETImage($this->imagePath);

    // Log consumer activity to StatHat for monitoring
    if ($result[1] == 200) {
      $this->statHat->ezCount('mbc-image-processor: MBC_ImageProcessor->process()', 1);
    }
    else {
      echo '** FAILED to GET ' . $this->imagePath  . ' image to trigger image style builds.', PHP_EOL;
      echo '------- mbc-image-processor - MBC_ImageProcessor->process(: $imagePath: ' . $this->imagePath . ' - ' . date('D M j G:i:s T Y') . ' -------', PHP_EOL;
      $this->statHat->ezCount('mbc-image-processor: MBC_ImageProcessor->process() FAILED', 1);
    }

  }

}
