<?php

namespace App\Http\Controllers;

use Trello\Client;

class BoardsController extends Controller
{
    /**
     * レビュー参加者の集計.
     *
     * @param string $key     API_KEY
     * @param string $token   TOKEN
     * @param string $boardId BoardId
     */
    public function assigner($key, $token, $boardId)
    {
        $client = new Client();
        $client->authenticate($key, $token, Client::AUTH_URL_CLIENT_ID);

        // listのデータを取得
        $lists = $client->api('board')->lists()->all($boardId);

        // listからレビューに合格リストのIDを抽出
        $doneLists = $this->extractDoneLists($lists);

        $viewData = [];
        $index = 0;
        foreach ($doneLists as $doneList) {
            $allCards = $client->api('lists')->cards()->all($doneList['id']);

            $dayDiff = [];
            foreach ($allCards as $allCard) {
                $actions = $client->api('cards')->actions()->all($allCard['id']);
                $startDate = $this->getReviewStartDate($actions);
                $endDate = $this->getReviewEndDate($actions);
                $dayDiff[] = $this->dayDiff($startDate, $endDate);
            }
            $dayAverage = $this->dayAverage($dayDiff);

            $enoguhAssignerCards = $this->extractEnoughAssignerCards($allCards);

            $allCardsCount = count($allCards);
            $enoguhAssignerCount = count($enoguhAssignerCards);
            $enoguhAssignerRetio = round(($enoguhAssignerCount / $allCardsCount) * 100, 2);

            $viewData[$i]['listName'] = $doneList['name'];
            $viewData[$i]['allCardsCount'] = $allCardsCount;
            $viewData[$i]['enoguhAssignerCount'] = $enoguhAssignerCount;
            $viewData[$i]['enoguhAssignerRetio'] = $enoguhAssignerRetio;
            $viewData[$i]['dayAverage'] = $dayAverage;

            ++$index;
        }

        return view('assigner', array('assigners' => $viewData));
    }

    /**
     * レビューに合格レーンを返す.
     *
     * @param array $lists レーンのリスト
     *
     * @return array
     */
    private function extractDoneLists($lists)
    {
        if (empty($lists)) {
            return [];
        }

        $returnArray = [];
        foreach ($lists as $list) {
            if (strpos($list['name'], 'レビューに合格') === 0) {
                $returnArray[] = $list;
            }
        }

        return $returnArray;
    }

    /**
     * 3人以上のレビューアがいるカードを返す.
     *
     * @param array $cards カードのリスト
     *
     * @return array
     */
    private function extractEnoughAssignerCards($cards)
    {
        $returnArray = [];
        foreach ($cards as $card) {
            if (count($card['idMembers']) > 2) {
                $returnArray[] = $card;
            }
        }

        return $returnArray;
    }

    /**
     * レビュー開始日を返す.
     *
     * @param array $cardActions cardActions
     *
     * @return string
     */
    private function getReviewStartDate($cardActions)
    {
        foreach ($cardActions as $cardAction) {
            if (!isset($cardAction['data']['listBefore']['name'])) {
                continue;
            }
            $before = $cardAction['data']['listBefore']['name'];
            if ($before === 'レビュー待ち') {
                return $cardAction['date'];
            }
        }

        return '';
    }

    /**
     * レビュー終了日を返す.
     *
     * @param array $cardActions cardActions
     *
     * @return string
     */
    private function getReviewEndDate($cardActions)
    {
        foreach ($cardActions as $cardAction) {
            if (!isset($cardAction['data']['listAfter']['name'])) {
                continue;
            }
            $after = $cardAction['data']['listAfter']['name'];
            if (strpos($after, 'レビューに合格') === 0) {
                return $cardAction['date'];
            }
        }

        return '';
    }

    /**
     * 日付が何日離れているか返す.
     *
     * @param string $start 開始日
     * @param string $end   終了日
     *
     * @return float
     */
    private function dayDiff($start, $end)
    {
        $startTime = strtotime($start);
        $endTime = strtotime($end);

        $secondDiff = abs($endTime - $startTime);

        return round($secondDiff / (60 * 60 * 24));
    }

    /**
     * 日付の平均値を返す.
     *
     * @param array $days 日付のリスト
     *
     * @return float
     */
    private function dayAverage($days)
    {
        $total = array_sum($days);

        return round($total / count($days), 2);
    }
}
