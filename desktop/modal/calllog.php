<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

$id = init('id');
$object = cmd::byId($id);
$bbox_id = $object->getEqLogic_id();
$bbox_obj = bbox_sagemcom::byId($bbox_id);

$useAPI = $bbox_obj->getConfiguration('BBOX_USE_API');
if ($useAPI != 'api') {
    throw new Exception('{{Fonction uniquement accessible aux BBoxs utilisants l\'API}}');
}

$calllog_obj = $bbox_obj->getCmd('info','calllog'); 
$calllogId=$calllog_obj->getId();
$calllogLogicalId=$calllog_obj->getLogicalId();
$data=$calllog_obj->execCmd(null, 2);
$calllog = json_decode($data);
$reversedCalllog = array_reverse($calllog);

$indent=1;

$dataInHtml = '<table class="bbox_table"><tr><th class="dataListe"></th><th class="dataListe">N°</th><th class="dataListe">Durée</th><th class="dataListe">Date</th></tr>';
foreach ($reversedCalllog as $key => $value) {
    $dataInHtml .= '<tr id="log' . $indent . '" class="dataListe">';
    $dataInHtml .= '<td class="dataListe">';
    switch ($value[0]) {
    case "A":
        $dataInHtml .= '<i class="icon techno-phone3" style="color: red;"></i>';
        break;
    case "E":
        $dataInHtml .= '<i class="icon techno-phone2" style="color: green;"></i>';
        break;
    case "R":
        $dataInHtml .= '<i class="icon techno-phone3" style="color: green;"></i>';
        break;
    } 
    $dataInHtml .= '</td>';
    $dataInHtml .= '<td class="dataListe">' . $value[1] . '</td>';
    $dataInHtml .= '<td class="dataListe">' . $value[2] . '</td>';
    $dataInHtml .= '<td class="dataListe">' . $value[3] . '</td></tr>';
    $indent++;
}
$dataInHtml.= '</table>';

echo <<<MON_HTML
 
<html>
<head>
</head>
<body>
   <style>
    table.bbox_table {
        border-collapse: collapse;
    }

    td.dataListe {
        border: 1px solid white;
        padding: 5px;
    }

    th.dataListe {
        border: 1px solid white;
        background:rgba(0,0,0,0.2);
        text-align:center;
    }

    tr.dataListe:nth-child(odd){
        background:rgba(0,0,0,0.1);
    }

</style>
<div style="min-width:50px;min-height:50px;" class="cmd tooltips cmd-widget container-fluid" data-type="info" data-subtype="other">
    <center>
        <span class="action" id="dataListe">${dataInHtml}</span>
    </center>
    <script>
    </script>
</div>
</body>
</html>
 
MON_HTML;

/*$bbox_sagemcom = bbox_sagemcom::byId($id);
	if (!is_object($bbox_sagemcom)) { 
			  
	 throw new Exception(__('Aucun equipement ne  correspond : Il faut (re)-enregistrer l\'équipement : ', __FILE__) . init('action'));
	 }*/
//$serveur = trim($bbox_sagemcom->getConfiguration('BBOX_SERVER_IP'));
//$link='http://'.$serveur;

?>