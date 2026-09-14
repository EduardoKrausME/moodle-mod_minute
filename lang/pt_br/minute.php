<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * minute.php
 *
 * @package   mod_minute
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['accuracy'] = 'Precisão (m)';
$string['captureteacherlocation'] = 'Capturar minha localização atual';
$string['characterlimit'] = 'Limite: {$a} caracteres';
$string['closed'] = 'Fechada';
$string['closedat'] = 'Esta atividade encerrou em {$a}.';
$string['closes'] = 'Encerra em {$a}';
$string['contentheader'] = 'Pergunta e resposta';
$string['currentreferencelocation'] = 'Localização de referência: {$a}';
$string['currentteacherip'] = 'IP de referência: {$a}';
$string['defaultprompt'] = 'Em aproximadamente um minuto, escreva o principal conceito que você aprendeu.';
$string['distance'] = 'Distância da referência';
$string['downloadcsv'] = 'Baixar CSV';
$string['erroraccuracy'] = 'A precisão da localização não pode ser negativa.';
$string['errorendbeforestart'] = 'O encerramento precisa ocorrer depois da abertura.';
$string['errorinvalidip'] = 'Informe um endereço IPv4 ou IPv6 válido.';
$string['errorlatitude'] = 'A latitude precisa estar entre -90 e 90.';
$string['errorlongitude'] = 'A longitude precisa estar entre -180 e 180.';
$string['errormaxchars'] = 'Informe um valor entre 20 e 2000 caracteres.';
$string['errorradius'] = 'Informe um raio entre 10 e 5000 metros.';
$string['errorreferencelocation'] = 'Capture a localização do professor antes de salvar quando a geolocalização estiver ativa.';
$string['eventcoursemoduleviewed'] = 'Papel de um minuto visualizado';
$string['eventresponsesubmitted'] = 'Resposta do Papel de um minuto enviada';
$string['eventresponseupdated'] = 'Resposta do Papel de um minuto atualizada';
$string['ipaddress'] = 'Endereço IP';
$string['ipcaptured'] = 'IP de referência do professor atualizado.';
$string['ipnotallowed'] = 'Seu endereço IP atual não corresponde ao IP de referência do professor.';
$string['iprestriction'] = 'Restrição por IP';
$string['lastsaved'] = 'Último salvamento: {$a}';
$string['latitude'] = 'Latitude';
$string['location'] = 'Localização';
$string['locationcaptured'] = 'Localização capturada.';
$string['locationcapturedsaving'] = 'Localização capturada. Salvando…';
$string['locationerror'] = 'Não foi possível obter sua localização. Verifique a permissão do navegador e o uso de HTTPS.';
$string['locationnotconfigured'] = 'A localização de referência do professor ainda não foi configurada.';
$string['locationnotconfiguredteacher'] = 'Ainda não há localização de referência do professor configurada.';
$string['locationoutsideradius'] = 'Você está a aproximadamente {$a->distance} m do ponto de referência. O raio permitido é de {$a->radius} m.';
$string['locationrequesting'] = 'Solicitando localização…';
$string['locationrequired'] = 'É necessário compartilhar a localização para enviar esta atividade.';
$string['locationrestriction'] = 'Restrição por localização';
$string['longitude'] = 'Longitude';
$string['maxchars'] = 'Máximo de caracteres';
$string['maxchars_help'] = 'Tamanho máximo da resposta do estudante. O valor permitido é de 20 a 2000 caracteres.';
$string['metres'] = '{$a} m';
$string['minute:addinstance'] = 'Adicionar uma nova atividade Papel de um minuto';
$string['minute:submit'] = 'Enviar resposta em uma atividade Papel de um minuto';
$string['minute:view'] = 'Visualizar uma atividade Papel de um minuto';
$string['minute:viewreport'] = 'Visualizar as respostas do Papel de um minuto';
$string['minutename'] = 'Nome da atividade';
$string['modulename'] = 'Papel de um minuto';
$string['modulenameplural'] = 'Papéis de um minuto';
$string['noinstances'] = 'Não há atividades Papel de um minuto neste curso.';
$string['notopenyet'] = 'Esta atividade abre em {$a}.';
$string['open'] = 'Aberta';
$string['opens'] = 'Abre em {$a}';
$string['participant'] = 'Participante';
$string['pluginadministration'] = 'Administração do Papel de um minuto';
$string['pluginname'] = 'Papel de um minuto';
$string['presenceheader'] = 'Validação de presença';
$string['privacy:metadata:minute_responses'] = 'Armazena a resposta de cada participante e, quando ativados pelo professor, os dados usados na validação de presença.';
$string['privacy:metadata:minute_responses:accuracy'] = 'Precisão da geolocalização informada pelo navegador, em metros.';
$string['privacy:metadata:minute_responses:ipaddress'] = 'Endereço IP detectado quando a resposta foi salva, quando a validação por IP está ativa.';
$string['privacy:metadata:minute_responses:latitude'] = 'Latitude fornecida pelo navegador quando a geolocalização está ativa.';
$string['privacy:metadata:minute_responses:longitude'] = 'Longitude fornecida pelo navegador quando a geolocalização está ativa.';
$string['privacy:metadata:minute_responses:response'] = 'Texto enviado pelo usuário.';
$string['privacy:metadata:minute_responses:timecreated'] = 'Momento em que a resposta foi criada.';
$string['privacy:metadata:minute_responses:timemodified'] = 'Momento da última atualização da resposta.';
$string['privacy:metadata:minute_responses:userid'] = 'Usuário que enviou a resposta.';
$string['prompt'] = 'Pergunta';
$string['question'] = 'Papel de um minuto';
$string['radiusdisplay'] = 'Raio: {$a} m';
$string['radiusmeters'] = 'Raio permitido (metros)';
$string['radiusmeters_help'] = 'Distância máxima entre a localização informada pelo navegador do estudante e a localização de referência do professor. O valor permitido é de 10 a 5000 metros.';
$string['referencelat'] = 'Latitude do professor';
$string['referencelon'] = 'Longitude do professor';
$string['reporttitle'] = 'Respostas: {$a}';
$string['requireip'] = 'Exigir o mesmo endereço IP do professor';
$string['requireip_help'] = 'O estudante só consegue enviar se o Moodle detectar exatamente o mesmo endereço IP público/do cliente configurado para o professor. Funciona bem quando professor e estudantes estão na mesma rede, mas pode falhar com rede móvel, VPN, proxy ou proxy reverso configurado incorretamente.';
$string['requirelocation'] = 'Exigir geolocalização próxima do professor';
$string['requirelocation_help'] = 'O estudante precisa autorizar a geolocalização no navegador e estar dentro do raio configurado a partir da posição de referência do professor. Normalmente o navegador exige HTTPS para fornecer geolocalização.';
$string['response'] = 'Resposta';
$string['responsecount'] = '{$a} resposta(s)';
$string['responseempty'] = 'Escreva uma resposta antes de enviar.';
$string['responses'] = 'respostas';
$string['responsesaved'] = 'Sua resposta foi salva.';
$string['responsetoolong'] = 'A resposta ultrapassa o limite de {$a} caracteres.';
$string['sharelocation'] = 'Compartilhar minha localização';
$string['status'] = 'Status';
$string['studentipnotice'] = 'A resposta será aceita somente se o Moodle detectar o mesmo endereço IP configurado como referência do professor.';
$string['submissionclosed'] = 'Esta atividade não está aceitando respostas neste momento.';
$string['submitresponse'] = 'Enviar resposta';
$string['submittedat'] = 'Último envio';
$string['teachercontrols'] = 'Controles do professor';
$string['teacherip'] = 'IP de referência do professor';
$string['teacherip_help'] = 'Endereço IP que os estudantes precisam utilizar. Inicialmente a atividade usa o IP detectado quando o professor salva a configuração; ele também pode atualizar essa referência na página da atividade.';
$string['teacherlocationcaptured'] = 'Localização de referência do professor atualizada.';
$string['teachernotconfigured'] = 'Ainda não há IP de referência configurado.';
$string['timeend'] = 'Encerrar em';
$string['timestart'] = 'Abrir a partir de';
$string['updateresponse'] = 'Atualizar resposta';
$string['usecurrentip'] = 'Usar meu IP atual';
$string['usecurrentlocation'] = 'Usar minha localização atual';
$string['viewreport'] = 'Ver respostas';
$string['yourresponse'] = 'Sua resposta';
