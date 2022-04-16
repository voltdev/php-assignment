<?php

declare(strict_types = 1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use DateTime;
use \SocialPost\Dto\SocialPostTo;
use \Statistics\Calculator\MaxPostLength;
use \Statistics\Dto\ParamsTo;

/**
 * Class ATestTest
 *
 * @package Tests\unit
 */
class TestTest extends TestCase
{
    /**
     * @test
     */
    public function testNothing(): void
    {
        $this->assertTrue(true);
    }
    
    private MaxPostLength $maxPostLengthCalc;
    
    /**
     * setup method used to prepare data before running the tests
     */
    protected function setUp(): void
    {
        $responseMockup = json_decode(file_get_contents("./tests/data/social-posts-response.json"), true)['data']['posts'];
                
        $this->maxPostLengthCalc = new MaxPostLength();
        
        $responseArr = array();
        foreach($responseMockup as $postData){
            $responseArr[count($responseArr)] =            
                (new SocialPostTo())
                ->setId($postData['id'] ?? null)
                ->setAuthorName($postData['from_name'] ?? null)
                ->setAuthorId($postData['from_id'] ?? null)
                ->setText($postData['message'] ?? null)
                ->setType($postData['type'] ?? null)
                ->setDate(DateTime::createFromFormat(DateTime::ATOM, $postData['created_time'] ?? null));
        }
        
        $params = (new ParamsTo())
                ->setStatName("Max Length Post")
                ->setStartDate(DateTime::createFromFormat(DateTime::ATOM, '2017-04-16T06:38:54+00:00'))
                ->setEndDate(DateTime::createFromFormat(DateTime::ATOM,   '2022-04-30T06:38:54+00:00'));
        $this->maxPostLengthCalc->setParameters($params);
        
        foreach($responseArr as $data){
            $this->maxPostLengthCalc->accumulateData($data);
        }
    }
    
    /**
     * @test testing the calculation of the longest post length
     */
    public function testLongestPostLength(){
        $expectedValue = 638;
        $statistics = $this->maxPostLengthCalc->calculate();
        
        $this->assertEquals($expectedValue, $statistics->getValue());
    }
}
