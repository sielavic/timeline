  public function getTimelineNewsAndPolls($ajax = null) {
        if($ajax){
            $secondNews = $_SESSION["secondNews"];
        } else {
            $secondNews = date('Y-m-d H:i:s');
            $fixed_news = \Entity\InformationFeed::select('information_feed.*')->where('information_feed.status', 0)->where('information_feed.draft', 0)->where('information_feed.fixed', 1)->first();
            $update_news_one = \Entity\InformationFeed::select('information_feed.*')->where('information_feed.status', 0)->where('information_feed.draft', 0)->where('information_feed.fixed', 0)->where('information_feed.object_type', 5)->where('information_feed.user_id', USER_COOKIE_ID)->orderBy('date', 'DESC')->first();
        }


        $update_news = \Entity\InformationFeed::select('information_feed.*')->where('information_feed.status', 0)->where('information_feed.draft', 0)->where('information_feed.fixed', 0)->where('information_feed.object_type', 5)->where('information_feed.user_id', USER_COOKIE_ID)->get();


        $getTimeLine = \Entity\InformationFeed::select('information_feed.*')
            ->where('information_feed.status', 0)
            ->where('information_feed.fixed', 0)
            ->where('information_feed.draft', 0)
            ->where('information_feed.object_type', '!=' , 5)
            ->where('information_feed.date', '<', $secondNews)
            ->orderBy('information_feed.date', 'DESC')
            ->get();
//        $show_view = $this->modules_model->getNEwTask(USER_COOKIE_ID, 8);


        if(isset($update_news)) {
            foreach ($update_news as $update_new){
                foreach ($getTimeLine as $timeLinee){
                    if ($timeLinee->date > $update_new->date){
                        if (!$getTimeLine->contains('date', $update_new->date)) {
                            $getTimeLine->prepend($update_new);
                        }
                    }
                }
            }
        }


        if(isset($update_news_one)) {
                    if (!$getTimeLine->contains('date', $update_news_one->date)) {
                        $getTimeLine->prepend($update_news_one);
                    }
        }



        $getTimeLine = $getTimeLine->sortByDesc('date')->take(10);

        if(isset($fixed_news)) {
            $getTimeLine->prepend($fixed_news);
        }

        ob_start();
        include "./application/views/main/information_feed.php";
        $result = ob_get_contents();
        ob_clean();
        echo json_encode(array('status' => 'success', 'data' => $result));
    }
