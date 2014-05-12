<?php

/**
 * Description of HtmlSearchOptions
 *
 * @author David Andrés Manzano Herrera - Damanzano
 * @since 2011-12-23
 * @package view
 */
class HtmlSearchOptions {

    /**
     * Prints selects options with the given community's tree structure
     * 
     * @author damanzano  
     * @since 2011-12-23
     * @param string $communityId
     * @param boolean $showCollections
     * @param boolean $showTopTitle
     * @param int $depth
     * @param boolean $subComunitiesAsValues
     */
    public static function communityScopes($communityId, $showCollections=true, $showTopTitle=true, $depth=-1, $subcommunitiesAsValues=false) {
        $xmlStructure = simplexml_load_file("config/categorysearch_config.xml");
        $html = '';
        $communities = $xmlStructure->community;
        $communityFound = false;
        for ($i = 0; (($i < $communities->count()) && (!$communityFound)); $i++) {
            $community = $communities[$i];
            //process the given community
            $theCommunity=self::lookForCommunity($communityId, $community);
            //print_r($theCommunity);
            if ($theCommunity!=null) {                
                $communityFound = true;
                $html = self::communityOptions($theCommunity, $html, $showCollections, $showTopTitle, $depth, $subcommunitiesAsValues);
            }
        }
        
        return $html;
    }

    private static function lookForCommunity($communityId, $xmlCommunity) {
        $foundCommunity = null;       
        
        if ($xmlCommunity["identifier"] == $communityId) {
            $foundCommunity = $xmlCommunity;            
            return $foundCommunity;
        } else {
            $subcommunities = $xmlCommunity->community;
            if ($subcommunities != null && $subcommunities->count() > 0) {
                foreach ($subcommunities as $subcommunity) {
                    $foundCommunity=  self::lookForCommunity($communityId, $subcommunity);
                    if ($foundCommunity!=null){
                        return $foundCommunity;
                    }
                }
            }
        }
        return $foundCommunity;
    }

    static private function communityOptions($xmlCommunity, $html, $showCollections=true, $showOwnTitle=true, $depth=-1, $subcommunitiesAsValues=false, $start=0) {
        if ($depth != 0) {
            
            if ($showOwnTitle && $start==0) {
                $html.='<option value="' . $xmlCommunity["identifier"] . '" data-level="'.$start.'" data-subject="' . $xmlCommunity->name . ' ">' . $xmlCommunity->name . ' - Todo</option>';
            }else {
                $html.='<option value="' . $xmlCommunity["identifier"] . '" data-level="'.$start.'" data-subject="' . $xmlCommunity->name . '">' . $xmlCommunity->name . ' </option>';
            }

            $subcommunities = $xmlCommunity->community;
            //echo "Sub comunidades:" . $subcommunities->count();

            if ($subcommunities != null && $subcommunities->count() > 0) {

                foreach ($subcommunities as $subcommunity) {
                    
//                    if ($subcommunitiesAsValues && !$showOwnTitle) {
//                        $html.= '<option value="' . $subcommunity["identifier"] . '" data-subject="' . $subcommunity->name . '">' . $subcommunity->name . '</option>';
//                    }
                    
                    if ($depth == -1) {
                        $html = self::communityOptions($subcommunity, $html, $showCollections, false, $depth, false, $start+1);
                    } else {
                        $html = self::communityOptions($subcommunity, $html, $showCollections, false, $depth - 1, false, $start+1);
                    }
                }
            }


            if ($showCollections) {
                $collections = $xmlCommunity->collection;
                //echo "coleciones:" . $collections->count();
                if ($collections != null && $collections->count() > 0) {
                    foreach ($collections as $collection) {
                        $collectionName = $collection->name;
                        $collectionName = str_replace("APFFVC - ", "", $collectionName);
                        $collectionName = str_replace("FCCV - ", "", $collectionName);
                        $collectionName = str_replace("FFDO - ", "", $collectionName);
                        $collectionName = str_replace(" - Patrimonial", "", $collectionName);
                        $html.='<option value="' . $collection["identifier"] . '" data-level="leaf" data-subject="' . $collectionName . '">' . $collectionName . '</option>';
                    }
                }
            }


            return $html;
        } else {
            return $html;
        }
    }

}

?>
