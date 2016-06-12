<?php

namespace App\Http\Controllers;

use Trello\Client;
use App\Http\Controllers\Controller;

class BoardsController extends Controller
{
    const KEY = '950cbf49df04e3d5716da5dd240ac39a';
    const TOKEN = 'ca2fd0dcfdd283d0d767531301860c27db8437cc5e74d89e44689837109bc4b7';

    private $client;

    private $boardId = '571d3ece9c1cf9e3b1c90732';

    public function __construct()
    {
        $this->client = new Client();
        $this->client->authenticate(self::KEY, self::TOKEN, Client::AUTH_URL_CLIENT_ID);
    }

    public function reviewAssigner()
    {
        // listのデータを取得
        $lists = $this->client->api('board')->lists()->all($this->boardId);

        // listからレビューに合格リストのIDを抽出
        $doneLists = $this->extractDoneLists($lists);

        $viewData = array();
        $i = 0;
        foreach ($doneLists as $doneList) {
            $allCards = $this->client->api('lists')->cards()->all($doneList['id']);
            $enoguhAssignerCards = $this->extractEnoughAssignerCards($allCards);

            $allCardsCount = count($allCards);
            $enoguhAssignerCardsCount = count($enoguhAssignerCards);
            $enoguhAssignerRetio = round(($enoguhAssignerCardsCount / $allCardsCount) * 100, 2);

            $viewData[$i]['listName'] = $doneList['name'];
            $viewData[$i]['allCardsCount'] = count($allCards);
            $viewData[$i]['enoguhAssignerCardsCount'] = count($enoguhAssignerCards);
            $viewData[$i]['enoguhAssignerRetio'] = round(($enoguhAssignerCardsCount / $allCardsCount) * 100, 2);

            $i++;

            // 'カードの数 : ' . $allCardsCount . '<br />' . PHP_EOL;
            // echo '3人以上のレビューアがいるカード : ' . $enoguhAssignerCardsCount . '<br />' . PHP_EOL;
            // echo '3人以上にレビューされている割合 : ' . $enoguhAssignerRetio . '%' . '<br />' . PHP_EOL;
        }

        return view('enoughAssigner', array('reviewAssigners' => $viewData));
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
