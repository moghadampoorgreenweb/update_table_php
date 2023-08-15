<?php
      $oldTable   =  collect(json_decode(file_get_contents(__DIR__.'/exportOldTable.json'),true));
      $targetTable =  collect(json_decode(file_get_contents(__DIR__.'/exportTargetTable.json'),true));
      $targetTable = $targetTable->pluck('value','key');
      $oldTable   = $oldTable->pluck('value','key');
      $outQuery   =  $targetTable->mapWithKeys(function ($value, $resipient) use ($oldTable){

          /*
          |--------------------------------------------------------------------------
          | condition update
          |--------------------------------------------------------------------------
          */
         if (isset($oldTable[$resipient]) && !empty($oldTable[$resipient]))
          return [$resipient => $oldTable[$resipient]];

          return [$resipient => $value];

        })->map(fn ($value,$resipient)=> [
                'UPDATE `targetTable` SET `targetColumn` =',
                '\'',
                 "{$value}",
                '\'',
                " WHERE `black_lists`.`recipient` = $resipient;"
        ])->map(fn ($resipient) => implode('',$resipient))->values()->toArray();

      
      file_put_contents(__DIR__.'/out.json',json_encode(implode(' ',$outQuery),JSON_UNESCAPED_UNICODE));
      die;




/*
---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
*/





          /*
          |--------------------------------------------------------------------------
          | manage srting start and end
          |--------------------------------------------------------------------------
          */
        function deleteLinesFromStart($string, $lineCount) {
            $lines = explode("\n", $string);
            $remainingLines = array_slice($lines, $lineCount);
            $result = implode("\n", $remainingLines);
            return $result;
        }
        function deleteLinesFromEnd($string, $lineCount) {
            $lineBreaks = substr_count($string, "\n");
            $linesToDelete = min($lineBreaks, $lineCount);

            $lastLineIndex = strlen($string) - 1;
            for ($i = 0; $i < $linesToDelete; $i++) {
                $lastLineIndex = strrpos($string, "\n", $lastLineIndex - strlen($string) - 1);
            }

            if ($lastLineIndex !== false) {
                $string = substr($string, 0, $lastLineIndex);
            }

            return $string;
        }
        
        $tblemailtemplates = collect(json_decode(file_get_contents(__DIR__.'/tblemailtemplates.json'),true)[2]['data']);

        foreach ($tblemailtemplates->pluck('message_fa') as $htm) {
            
            $result = deleteLinesFromStart($htm, 27);
            $result = deleteLinesFromEnd($result, 22);
            
           if (preg_match("/<\/td>\s*<\/tr>\s*$/",$result)) {
               $result = deleteLinesFromEnd($result, 2);
           }
           
            $outPut[]  = $result;
        }


    /*
    |-------------------------------------------------------------
    | update email
    |-------------------------------------------------------------
    */
        
    function deleteLinesFromStart($string, $lineCount) {
        $lines = explode("\n", $string);
        $remainingLines = array_slice($lines, $lineCount);
        $result = implode("\n", $remainingLines);
        return $result;
    }

    function deleteLinesFromEnd($string, $lineCount) {
        $lineBreaks = substr_count($string, "\n");
        $linesToDelete = min($lineBreaks, $lineCount);

        $lastLineIndex = strlen($string) - 1;
        for ($i = 0; $i < $linesToDelete; $i++) {
            $lastLineIndex = strrpos($string, "\n", $lastLineIndex - strlen($string) - 1);
        }

        if ($lastLineIndex !== false) {
            $string = substr($string, 0, $lastLineIndex);
        }

        return $string;
    }

    $tblemailtemplates = collect(json_decode(file_get_contents(__DIR__.'/tblemailtemplates_diff.json'),true)[2]['data']);

    $ids=$tblemailtemplates->pluck('id');
    $subjectsByIds =  $tblemailtemplates->pluck('subject_fa','id');
    $nameByIds = $tblemailtemplates->pluck('name','id');
    $typeByIds = $tblemailtemplates->pluck('type','id');
    $disabledByIds = $tblemailtemplates->pluck('disabled','id');
    $customByIds = $tblemailtemplates->pluck('custom','id');

    foreach ($tblemailtemplates->pluck('message_fa','id') as $key => $htm) {

        /*
        |--------------------------------------------------------------------------
        | 90
        |--------------------------------------------------------------------------
        |
        */
//            $result = deleteLinesFromStart($htm, 27);
//            $result = deleteLinesFromEnd($result, 22);
//

//           if (preg_match("/<\/td>\s*<\/tr>\s*$/",$result)) {
//               $result = deleteLinesFromEnd($result, 2);
//           }

//           if (preg_match("/<table[^>]*>.*?<tbody>/s",$result)) {
//                $result = deleteLinesFromStart($htm, 3);
//                $result = deleteLinesFromEnd($result, 3);
//           }

        /*
        |--------------------------------------------------------------------------
        | 40
        |--------------------------------------------------------------------------
        |
        */
       if (preg_match("/<td[^>]*>.*?<table [^>]*>/s",$htm)) {
            $result = deleteLinesFromStart($htm, 27);
            $result = deleteLinesFromEnd($result, 22);
       }else{
           $result = $htm;
       }

       if (preg_match("/<\/tbody[^>]*>.*?<\/table>/s",$result)) {
            $result = deleteLinesFromStart($result, 2);
       }



       $subject = $subjectsByIds[$key];
       $disabled = ($disabledByIds[$key] == 0) ? 1 : 0;
       $custom = $customByIds[$key];
       $type = ucfirst($typeByIds[$key]);
       $nameByIds = $nameByIds->unique();
       $isname = isset($nameByIds[$key]);
       $result = str_replace('\'','\"',$result);

       if (!$isname){

           continue;
       }

        $name = $nameByIds[$key];

         $que = [
               'INSERT INTO `email_templates`  (`id`, `name`, `subject`, `html`, `description`, `enable`, `no_layout`,
                               `category`, `is_custom`, `created_at`, `updated_at`)',
               " VALUES ('{$key}','{$name}','{$subject}','{$result}',NULL, '{$disabled}', '0', '{$type}', '{$custom}', NULL, NULL);",
           ];

        $outPut[]  = implode('',$que) ;
    }



    file_put_contents(__DIR__.'/text.json',json_encode(implode(' ',$outPut)));
    die;

    function deleteLinesFromStart($string, $lineCount) {
        $lines = explode("\n", $string);
        $remainingLines = array_slice($lines, $lineCount);
        $result = implode("\n", $remainingLines);
        return $result;
    }

    function deleteLinesFromEnd($string, $lineCount) {
        $lineBreaks = substr_count($string, "\n");
        $linesToDelete = min($lineBreaks, $lineCount);

        $lastLineIndex = strlen($string) - 1;
        for ($i = 0; $i < $linesToDelete; $i++) {
            $lastLineIndex = strrpos($string, "\n", $lastLineIndex - strlen($string) - 1);
        }

        if ($lastLineIndex !== false) {
            $string = substr($string, 0, $lastLineIndex);
        }

        return $string;
    }

    $tblemailtemplates = collect(json_decode(file_get_contents(__DIR__.'/tblemailtemplates_diff.json'),true)[2]['data']);

    $ids=$tblemailtemplates->pluck('id');
    $subjectsByIds =  $tblemailtemplates->pluck('subject_fa','id');
    $nameByIds = $tblemailtemplates->pluck('name','id');
    $typeByIds = $tblemailtemplates->pluck('type','id');
    $disabledByIds = $tblemailtemplates->pluck('disabled','id');
    $customByIds = $tblemailtemplates->pluck('custom','id');

    foreach ($tblemailtemplates->pluck('message_fa','id') as $key => $htm) {

        /*
        |--------------------------------------------------------------------------
        | 90
        |--------------------------------------------------------------------------
        |
        */
//            $result = deleteLinesFromStart($htm, 27);
//            $result = deleteLinesFromEnd($result, 22);
//

//           if (preg_match("/<\/td>\s*<\/tr>\s*$/",$result)) {
//               $result = deleteLinesFromEnd($result, 2);
//           }

//           if (preg_match("/<table[^>]*>.*?<tbody>/s",$result)) {
//                $result = deleteLinesFromStart($htm, 3);
//                $result = deleteLinesFromEnd($result, 3);
//           }

        /*
        |--------------------------------------------------------------------------
        | 40
        |--------------------------------------------------------------------------
        |
        */
       if (preg_match("/<td[^>]*>.*?<table [^>]*>/s",$htm)) {
            $result = deleteLinesFromStart($htm, 27);
            $result = deleteLinesFromEnd($result, 22);
       }else{
           $result = $htm;
       }

       if (preg_match("/<\/tbody[^>]*>.*?<\/table>/s",$result)) {
            $result = deleteLinesFromStart($result, 2);
       }



       $subject = $subjectsByIds[$key];
       $disabled = ($disabledByIds[$key] == 0) ? 1 : 0;
       $custom = $customByIds[$key];
       $type = ucfirst($typeByIds[$key]);
       $nameByIds = $nameByIds->unique();
       $isname = isset($nameByIds[$key]);
       $result = str_replace('\'','\"',$result);

       if (!$isname){

           continue;
       }

        $name = $nameByIds[$key];

         $que = [
               'INSERT INTO `email_templates`  (`id`, `name`, `subject`, `html`, `description`, `enable`, `no_layout`,
                               `category`, `is_custom`, `created_at`, `updated_at`)',
               " VALUES ('{$key}','{$name}','{$subject}','{$result}',NULL, '{$disabled}', '0', '{$type}', '{$custom}', NULL, NULL);",
           ];

        $outPut[]  = implode('',$que) ;
    }



    file_put_contents(__DIR__.'/text.json',json_encode(implode(' ',$outPut)));
    die;
