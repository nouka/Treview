<?php

namespace App\Http\Controllers;

use Trello\Client;
use App\Http\Controllers\Controller;

class BoardsController extends Controller
{
    /**
     * レビュー参加者の集計
     *
     * @param string $key   API_KEY
     * @param string $token TOKEN
     * @param string $id    BoardId
     *
     * @return void
     */
    public function assigner($key, $token, $id)
    {
        $client = new Client();
        $client->authenticate($key, $token, Client::AUTH_URL_CLIENT_ID);

        // listのデータを取得
        $lists = $client->api('board')->lists()->all($id);

        // listからレビューに合格リストのIDを抽出
        $doneLists = $this->extractDoneLists($lists);

        $viewData = array();
        $i = 0;
        foreach ($doneLists as $doneList) {
            $allCards = $client->api('lists')->cards()->all($doneList['id']);
            $enoguhAssignerCards = $this->extractEnoughAssignerCards($allCards);

            $allCardsCount = count($allCards);
            $enoguhAssignerCardsCount = count($enoguhAssignerCards);
            $enoguhAssignerRetio = round(($enoguhAssignerCardsCount / $allCardsCount) * 100, 2);

            $viewData[$i]['listName'] = $doneList['name'];
            $viewData[$i]['allCardsCount'] = count($allCards);
            $viewData[$i]['enoguhAssignerCardsCount'] = count($enoguhAssignerCards);
            $viewData[$i]['enoguhAssignerRetio'] = round(($enoguhAssignerCardsCount / $allCardsCount) * 100, 2);

            $i++;
        }

        return view('assigner', array('assigners' => $viewData));
    }

    private function extractDoneLists($lists)
    {
        if (empty($lists)) {
            return array();
        }

        $returnArray = array();
        foreach ($lists as $list) {
            if (strpos($list['name'], 'レビューに合格') === 0) {
                $returnArray[] = $list;
            }
        }

        return $returnArray;
    }

    private function extractEnoughAssignerCards($cards)
    {
        $returnArray = array();
        foreach ($cards as $card) {
            if (count($card['idMembers']) > 2) {
                $returnArray[] = $card;
            }
        }

        return $returnArray;
    }
}
