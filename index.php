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

