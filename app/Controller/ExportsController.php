<?php
App::uses('AppController', 'Controller');
/**
 * Exports Controller
 *
 * @property Export $Export
 * @property PaginatorComponent $Paginator
 */
class ExportsController extends AppController {

  public $uses = array('Artefact', 'BrowsingSession');

/**
 * Components
 *
 * @var array
 */
  public function export_data(){
    ini_set('memory_limit','1000M');
    ini_set('max_execution_time', 300);

    $this->layout = 'ajax';
    $this->autoRender = false;

    $browsing_sessions = $this->BrowsingSession->getAll();

    $export_data = array();


    $action_lookup = array();

    $action_types = array(
      'artefact_click' => 'artefacts_viewed_count',
      'remove_artefact' => 'remove_artefact_count',
      'remove_artefact_from_historybar' => 'remove_artefact_from_historybar_count',
      'open_history_panel' => 'open_history_panel_count',
      'close_history_panel' => 'close_history_panel_count',
      'information_button_click' => 'information_button_click_count',
      'zoom_button_click' => 'zoom_button_click_count',
      'open_map_view' => 'open_map_view_count',
      'close_map_view' => 'close_map_view_count',
      'carousel' => 'carousel_click_count',
      'fetch_more_event' => 'fetch_more_event_count',
      'map_fetch_artefacts' => 'map_fetch_artefacts_count',
      'map_fetch_keywords' => 'map_fetch_keywords_count',
      'share_history_facebook' => 'share_history_facebook_count',
      'share_history_twitter' => 'share_history_twitter_count',
      'share_history_email' => 'share_history_email_count',
      'share_history_link' => 'share_history_link_count',
      'share_item_facebook' => 'share_item_facebook_count',
      'share_item_twitter' => 'share_item_twitter_count',
      'share_item_email' => 'share_item_email_count',
      'share_item_link' => 'share_item_link_count',
      'dive_from_map_view' => 'dive_from_map_view_count',
      'artefact_tile_hover' => 'artefact_tile_hover_count'
    );

    $rows = array();

    foreach($browsing_sessions as $s)
    {
      $row = array();

      $row['session_id'] = (string) $s['_id'];
      if(array_key_exists('u_id', $s))
      {
        $row['user_id'] = str_replace('; path=/', '', str_replace('pastpaths_u_id=', '', $s['u_id']));
      }
      else
      {
        $row['user_id'] = NULL;
      }

      $row['start_time'] = gmdate('Y-m-d H:i:s', $s['start_time']);
      $row['end_time'] = gmdate('Y-m-d H:i:s', $s['end_time']);
      $row['duration_in_seconds'] = $s['end_time'] - $s['start_time'];

      if($row['duration_in_seconds'] > 0)
      {
        if(array_key_exists('actions', $s))
        {
          $row['actions_total'] = count($s['actions']);
        }
        else
        {
          $row['actions_total'] = 0;
        }

        $row['artefacts_viewed_count'] = 0;
        $row['artefact_tile_hover_count'] = 0;
        $row['information_button_click_count'] = 0;
        $row['zoom_button_click_count'] = 0;
        $row['remove_artefact_count'] = 0;
        $row['remove_artefact_from_historybar_count'] = 0;
        $row['fetch_more_event_count'] = 0;
        $row['share_history_facebook_count'] = 0;
        $row['share_history_twitter_count'] = 0;
        $row['share_history_email_count'] = 0;
        $row['share_history_link_count'] = 0;
        $row['share_item_facebook_count'] = 0;
        $row['share_item_twitter_count'] = 0;
        $row['share_item_email_count'] = 0;
        $row['share_item_link_count'] = 0;
        $row['open_map_view_count'] = 0;
        $row['close_map_view_count'] = 0;
        $row['carousel_click_count'] = 0;
        $row['map_fetch_artefacts_count'] = 0;
        $row['map_fetch_keywords_count'] = 0;
        $row['dive_from_map_view_count'] = 0;
        $row['open_history_panel_count'] = 0;
        $row['close_history_panel_count'] = 0;
        $row['carousel_click_count'] = 0;
        $row['last_observed_event_datetime'] = 'n/a';
        $row['calculated_session_duration'] = 'n/a';
        if(array_key_exists('actions', $s))
        {
          $latest_action = 0;
          foreach($s['actions'] as $action)
          {
            if(array_key_exists($action['action'], $action_types))
            {
              if(strtotime($action['created']) > $latest_action)
              {
                $latest_action = $action['created'];
              }

              $row[$action_types[$action['action']]]++;
            }
          }

          if($latest_action > 0)
          {
            $row['last_observed_event_datetime'] = $latest_action;
            $row['calculated_session_duration'] =  strtotime($latest_action) - $s['start_time'];
          }
        }

        $rows[] = $row;
      }
    }

    $filename = "data_export_" . date("Y-m-d") . ".csv";
    $now = gmdate("D, d M Y H:i:s");
      header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
      header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
      header("Last-Modified: {$now} GMT");

      // force download
      header("Content-Type: application/force-download");
      header("Content-Type: application/octet-stream");
      header("Content-Type: application/download");

      // disposition / encoding on response body
      header("Content-Disposition: attachment;filename={$filename}");
      header("Content-Transfer-Encoding: binary");

    echo $this->array2csv($rows);
  }

  private function array2csv(array &$array)
  {
     if (count($array) == 0) {
       return null;
     }
     ob_start();
     $df = fopen("php://output", 'w');
     fputcsv($df, array_keys(reset($array)));
     foreach ($array as $row) {
        fputcsv($df, $row);
     }
     fclose($df);
     return ob_get_clean();
  }

}
