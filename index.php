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
