<?php

/*Contributors: Kjersti Fagerholt, Roar Gj�vaag, Ragnhild Krogh, Espen Str�mjordet,
 Audun S�ther, Hanne Marie Trelease, Eivind Halm�y Wolden

 "Copyright 2015 The TAG CLOUD/SINTEF project

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License."
 */

/**
 * Class containing information related to stories
 * @author Audun S�ther
 * @author Kjersti Fagerholt
 * @author Eivind Halm�y Wolden
 * @author Hanne Marie Trelease
 */

require_once (__DIR__.'/../database/config.php');

class storyModel{
    private $storyId;
    private $title;
    private $creatorList;
    private $introduction;
    private $theStory;
    private $municipality; 
    private $county;
    private $rights;
    private $institution;
    private $imageList;
    private $videoList;
    private $audioList;
    private $subCategoryList;
    private $subCategoryNames;
    private $subjectList;
    private $url;
    private $categoryList;
    private $rating;
    private $explanation;
    private $falseRecommend;
    private $typeOfRecommendation;
    private $createdDate;
    private $numericalId;
    private $userTags = array();
	private $lat;
	private $long;

    /**
     * Constructor.
     * Retrieves story from digitalt fortalt api
     * @param String $id Digitalt fortalt storyId
     */
    public function getFromDF($id)
    {

        $xml_from_API = $this->file_get_contents_utf8(API_URL.'artifact?owner=H-DF&identifier='.$id.'&mapping=ABM&api.key='.API_KEY);
        $xml = simplexml_load_string($xml_from_API);
        
        $this->storyId = $id;
        $this->numericalId = explode('.', $id)[1];
        $this->title = (string) $xml->children('dc', TRUE)->title;
        $this->introduction = (string) $xml->children('abm', TRUE)->introduction;
        $this->theStory = (string) $xml->children('dc', TRUE)->description;
        $this->county = (string) $xml->children('abm', TRUE)->county;
        $this->municipality = (string) $xml->children('abm', TRUE)->municipality;
        $this->rights = (string) $xml->children('dc', TRUE)->rights;
        $this->institution = (string) $xml->children('europeana', TRUE)->dataProvider;
        $this->url = (string) $xml->children('europeana', TRUE)->isShownAt;
        $this->createdDate = (string) $xml->children('dcterms', TRUE)->created; 
		$this->lat = (string) $xml->children('abm', TRUE)->lat;
		$this->long = (string) $xml->children('abm', TRUE)->long;

        //Create a list of all creators for the story
        foreach ($xml->children('dc', TRUE)->creator as $element)
        {
            $this->creatorList[] = (string) $element;
        }
        //Create a list of all image IDs for the story
        foreach ($xml->children('abm', TRUE)->image as $element) 
        {
            $url = (string) $element->children('abm', TRUE)->imageUri;
            if(!empty($url)){
                $this->imageList[] = array( 
                    'imageurl' => $url,
                    'description' => (string) $element->children('dc', TRUE)->description);
            }
            
        }
        //Create a nested list of all video URLs with posters for the story
        foreach ($xml->children('abm', TRUE)->media as $element)
        {
            $url = (string) $element->children('abm', TRUE)->videoUri;
            if(!empty($url)){
                parse_str(parse_url($url, PHP_URL_QUERY), $urlquery);            
                $description = (string) $element->children('', TRUE)->description;
                if(!isset($urlquery['mmid'])){
                    $this->videoList[] = array('videourl' => $url,
                        'description' => $description);

                }else{
                    $this->videoList[] = array(
                        'videourl' => $url, 
                        'posterurl' => "http://mm01.dimu.no/image/".$urlquery['mmid'],
                        'description' => $description
                        );
                }

            }
        }
        //Create a list of all audio URLs for the story
        foreach ($xml->children('abm', TRUE)->media as $element)
        {
            $url = (string) $element->children('abm', TRUE)->soundUri;
            if(!empty($url)){
                $this->audioList[] = array( 
                    'audiourl' => $url,
                    'description' => (string) $element->children('', TRUE)->description);
            }
        }
        //Create a list of all category IDs for the story
        foreach ($xml->children('abm', TRUE)->classification as $element)
        {
            preg_match('/\d+/',(string) $element,$match); //Selects only the numerical category name
            $this->subCategoryList[] = $match[0];
            $name = substr((string) $element, 0, strpos((string) $element, '('));
            $this->subCategoryNames[] = strtolower((string) $name);
        }
        //Create a list of all subjects for the story        
        foreach ($xml->children('dc', TRUE)->subject as $element)
        {
            $this->subjectList[] = (string) $element;
        }
    }

    /**Gets story information stored in database, should take userId as parameter*/
    public function fromDB($data){
        $this->categoryList = explode(",", $data['categories']);
        if(array_key_exists('storedStory', $data)){
            $this->rating = $data['storedStory']['rating'];
            $this->explanation = $data['storedStory']['explanation'];
            $this->falseRecommend = $data['storedStory']['false_recommend'];
            $this->typeOfRecommendation = $data['storedStory']['type_of_recommendation'];
        }
        if(array_key_exists('tags', $data)){
            foreach ($data['tags'] as $tag) {
                array_push($this->userTags, $tag['tagName']);
            }
        }
    }

    //SETTERS
    public function setstoryId($storyId)
    {
        $this->storyId =$storyId;
    }
    
    public function settitle($title)
    {
        $this->title = $title;
    }
    
    public function setCreatorList( $creatorList)
    {
        $this->creatorList = $creatorList;
    }
    
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;
    }
    
    public function setTheStory($theStory)
    {
        $this->theStory = $theStory;
    }   
    
    public function setMunicipality($municipality)
    {
        $this->municipality = $municipality; 
    }   
    
    public function setCounty($county)
    {
        $this->county = $county;
    }
    
    public function setRights($rights)
    {
        $this->rights = $rights;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function setInstitution($institution)
    {
        $this->institution = $institution;
    }
    
    public function setImageList($imageList)
    {
        $this->imageList = $imageList;
    }
    
    public function setVideoList($videoList)
    {
        $this->videoList = $videoList;
    }
    
    public function setAudioList($audioList)
    {
        $this->audioList =$audioList;
    }

    public function setsubCategoryList($catergoryList)
    {
        $this->subCategoryList = $catergoryList;
    }
    
    public function setsubCategoryNames($subCategoryNames)
    {
        $this->subCategoryNames = $subCategoryNames;
    }

    public function setSubjectList($subjectList)
    {
        $this->subjectList = $subjectList;
    }
    
    public function setCategoryList($categoryList)
    {
        $this->categoryList = $categoryList;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }
    public function setNumericalId($numericalId){
        $this->numericalId = $numericalId;
    }
	public function setLat($lat){
		$this->lat = $lat;
	}
    public function setLong($long){
		$this->long = $long;
	}
	
    //GETTERS
    public function getstoryId()
    {
        return $this->storyId;
    }
    
    public function getnumericalId()
    {
        return $this->numericalId;
    }
    
    public function gettitle()
    {
        return $this->title;
    }
    
    public function getCreatorList()
    {
        return $this->creatorList;
    }
    
    public function getIntroduction()
    {
        return $this->introduction;
    }
    
    public function getTheStory()
    {
        return $this->theStory;
    }   
    
    public function getMunicipality()
    {
        return $this->municipality;
    }   
    
    public function getCounty()
    {
        return $this->county;
    }
    
    public function getDate(){
        return $this->createdDate;
    }
    
    public function getRights()
    {
        return $this->rights;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function getInstitution()
    {
        return $this->institution;
    }
    
    public function getImageList()
    {
        return $this->imageList;
    }
    
    public function getVideoList()
    {
        return $this->videoList;
    }
    
    public function getAudioList()
    {
        return $this->audioList;
    }
    public function getSubjectList()
    {
        return $this->subjectList;
    }
    public function getsubCategoryList()
    {
        return $this->subCategoryList;
    }
    public function getsubCategoryNames()
    {
        return $this->subCategoryNames;
    }
    public function getCategoryList(){
        return $this->categoryList;
    }
    public function getRating(){
        return $this->rating;
    }
    public function getLat(){
		return $this->lat;
	}
	public function getLong(){
		return $this->long;
	}
	
    /**
     * Returns all info in model as an array
     * @return storyArray
     */
    public function getAll(){
        return array(
            'storyId' => $this->getstoryId(),
            'title' =>  $this->gettitle(),
            'creatorList' => $this->getCreatorList(),
            'introduction' => $this->getIntroduction(),
            'theStory' => $this->getTheStory(),
            'municipality' => $this->getMunicipality(),
            'county' => $this->getCounty(),
            'rights' => $this->getRights(),
            'institution' => $this->getInstitution(),
            'imageList' => $this->getImageList(),
            'videoList' => $this->getVideoList(), 
            'audioList' => $this->getAudioList(),
            'subCategoryNames' => $this->getsubCategoryNames(),
            'url' => $this->getUrl(),
            'rating' => $this->getRating(),
            'categoryList' => $this->getCategoryList(),
            'typeOfRecommendation' => $this->typeOfRecommendation,
            'explanation' => $this->explanation,
            'falseRecommend' => $this->falseRecommend,
            'userTags' => $this->userTags,
			'latitude' => $this->lat,
			'longitude' => $this->long);
    }

    public function sendStory(){
      $data = get_class($this).getAll();
      echo json_encode($data);
      return json_encode($data);
    }

    //Helper functions
    public function print_all_info(){
        print_r('Story ID - '.$this->storyId.PHP_EOL.PHP_EOL);
        print_r('Title- '.$this->title.PHP_EOL.PHP_EOL);
        print_r('Creators- ');
        print_r($this->creatorList);
        print_r(PHP_EOL.PHP_EOL);
        print_r('Introduction- '.$this->introduction.PHP_EOL.PHP_EOL);
        print_r('The Story - '.$this->theStory.PHP_EOL.PHP_EOL);
        print_r('Municipality - '.$this->municipality.PHP_EOL.PHP_EOL);
        print_r('County - '.$this->county.PHP_EOL.PHP_EOL);
        print_r('Rights - '.$this->rights.PHP_EOL.PHP_EOL);
        print_r('Institution - '.$this->institution.PHP_EOL.PHP_EOL);
        print_r('ImageID List - ');
        print_r($this->imageList);
        print_r(PHP_EOL.PHP_EOL);
        print_r('VideoURL List - ');
        print_r($this->videoList);
        print_r(PHP_EOL.PHP_EOL);
        print_r('AudioURL List - ');
        print_r($this->audioList);
        print_r(PHP_EOL.PHP_EOL);
        print_r('Category List - ');
        print_r($this->subCategoryList);
        print_r(PHP_EOL.PHP_EOL);
        print_r('Subject List - ');
        print_r($this->subjectList);
        print_r(PHP_EOL.PHP_EOL);
    }

    /**
     * Needed because the normal file_get_contents is not unicode
     * @param unknown $fn
     * @return string
     */
    private function file_get_contents_utf8($fn) { //
        $content = file_get_contents($fn);
        return mb_convert_encoding($content, 'UTF-8',
           mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
    }
    
}

//$story = new storyModel(); //example usage
//$story->getFromDF('DF.5736');
//$story->print_all_info();

?>
