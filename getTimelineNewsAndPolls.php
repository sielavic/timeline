<?php
public function getTimelineNewsAndPolls($ajax = null) {
        if($ajax){
            $secondNews = $_SESSION["secondNews"];
        } else {
            $secondNews = date('Y-m-d H:i:s');
            $fixed_news = \Entity\InformationFeed::select('information_feed.*')->where('information_feed.status', 0)->where('information_feed.draft', 0)->where('information_feed.fixed', 1)->first();
            $update_news_one = \Entity\InformationFeed::select('information_feed.*')->where('information_feed.status','!=', 2)->where('information_feed.draft', 0)->where('information_feed.fixed', 0)->where('information_feed.object_type', 5)->where('information_feed.user_id', USER_COOKIE_ID)->orderBy('date', 'DESC')->first();
        }


//        $update_news = \Entity\InformationFeed::select('information_feed.*')->where('information_feed.status', 0)->where('information_feed.draft', 0)->where('information_feed.fixed', 0)->where('information_feed.object_type', 5)->where('information_feed.user_id', USER_COOKIE_ID)->get();
        $update_news = \Entity\InformationFeed::select('information_feed.*')->where('information_feed.status','!=', 0)->where('information_feed.draft', 0)->where('information_feed.fixed', 0)->where('information_feed.object_type', 5)->get();


        $getTimeLine = \Entity\InformationFeed::select('information_feed.*')
            ->where('information_feed.status', 0)
            ->where('information_feed.fixed', 0)
            ->where('information_feed.draft', 0)
            ->where('information_feed.object_type', '!=' , 5)
            ->where('information_feed.date', '<', $secondNews)
            ->orderBy('information_feed.date', 'DESC')
            ->get();
//        $show_view = $this->modules_model->getNEwTask(USER_COOKIE_ID, 8);


        if (isset($update_news) && !$update_news->isEmpty()) {
            foreach ($update_news as $update_new) {
                $is_user = \Entity\InformationFeed::where('object_id', $update_new->object_id)->where('user_id', USER_COOKIE_ID)->first();
                if (!isset($is_user)) {
                    $new_news = \Entity\InformationFeed::addFeed($update_new->object_id, 5, $update_new->date, USER_COOKIE_ID, 1, 1);
                    $getTimeLine->prepend($new_news);
                } else {
                    foreach ($getTimeLine as $timeLinee) {
                        if ($timeLinee->date > $update_new->date) {
                            if ($update_new->user_id == USER_COOKIE_ID) {
                                $is_user_status = \Entity\InformationFeed::where('object_id', $update_new->object_id)->where('user_id', USER_COOKIE_ID)->where('status', '!=', 2)->first();
                                if (isset($is_user_status)) {
                                    if (!$getTimeLine->contains('date', $update_new->date)) {
                                        $getTimeLine->prepend($update_new);
                                    }
                                }
                            }
                        }
                    }

                    if (isset($update_news_one)) {
                        if ($update_news_one->date > $update_new->date) {
                            if (!$getTimeLine->contains('date', $update_new->date)) {
                                if ($update_new->user_id == USER_COOKIE_ID) {
                                    $is_user_status = \Entity\InformationFeed::where('object_id', $update_new->object_id)->where('user_id', USER_COOKIE_ID)->where('status', '!=', 2)->first();
                                    if (isset($is_user_status)) {
                                        $getTimeLine->prepend($update_new);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
//                }
//            }else{
//
//                foreach ($update_news as $update_new) {
//                    foreach ($getTimeLine as $timeLinee) {
//                        if ($timeLinee->date > $update_new->date) {
//                            $is_user_status = \Entity\InformationFeed::where('object_id', $update_new->object_id)->where('user_id', USER_COOKIE_ID)->where('status', '!=', 2)->first();
//                            if (isset($is_user_status)) {
//                                $getTimeLine->prepend($update_new);
//                            }
//                        }
//                    }
//                    if (isset($update_news_one)){
//                        if ($update_news_one->date > $update_new->date){
//                            if (!$getTimeLine->contains('date', $update_new->date)) {
//                                $is_user_status = \Entity\InformationFeed::where('object_id', $update_new->object_id)->where('user_id', USER_COOKIE_ID)->where('status', '!=', 2)->first();
//                                if (isset($is_user_status)) {
//                                    $getTimeLine->prepend($update_new);
//                                }
//                            }
//                        }
//                    }
//                }
//            }




//        if(isset($update_news)) {
//            foreach ($update_news as $update_new){
//                foreach ($getTimeLine as $timeLinee){
//                    if ($timeLinee->date > $update_new->date ){
//                        if (!$getTimeLine->contains('date', $update_new->date)) {
//                            $getTimeLine->prepend($update_new);
//                        }
//                    }
//                }
//                if (isset($update_news_one)){
//                    if ($update_news_one->date > $update_new->date){
//                        if (!$getTimeLine->contains('date', $update_new->date)) {
//                            $getTimeLine->prepend($update_new);
//                        }
//                    }
//                }
//            }
//        }


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
