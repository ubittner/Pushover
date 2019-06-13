## Pushover

[![Version](https://img.shields.io/badge/Symcon_Version-5.1>-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
![Version](https://img.shields.io/badge/Modul_Version-1.00-blue.svg)
![Version](https://img.shields.io/badge/Modul_Build-1-blue.svg)
![Version](https://img.shields.io/badge/Code-PHP-blue.svg)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)  

![Logo](../imgs/pushover_logo.png)  

Pushover Modul für IP-Symcon - Ein Projekt von Ulrich Bittner

Mit diesem Modul ist es möglich Nachrichten via [Pushover](https://pushover.net) über [IP-Symcon](https://www.symcon.de) zu versenden. 

Hierfür ist erforderlich, dass ein Konto bei Pushover vorhanden ist.

Für die Einrichtung wird ein API Token und ein Benutzerschlüssel des Pushover Kontos benötigt.

Weitere Informationen zur API finden Sie hier: [Pushover API](https://pushover.net/api)

Für dieses Modul besteht kein Anspruch auf Fehlerfreiheit, Weiterentwicklung, sonstige Unterstützung oder Support.

Bevor das Modul installiert wird, sollte unbedingt ein Backup von IP-Symcon durchgeführt werden.

Der Entwickler haftet nicht für eventuell auftretende Datenverluste oder sonstige Schäden.

Der Nutzer stimmt den o.a. Bedingungen, sowie den Lizenzbedingungen ausdrücklich zu.



### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanz in IP-Symcon](#4-einrichten-der-instanz-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)


### 1. Funktionsumfang

* Versenden von Benachrichtigungen via Pushover
    * An alle im Konto vorhandenen Geräte
    * An ausgewählte Geräte
* Benachrichtigungen mit ausgewählter Priorität
* Benachrichtigungen mit ausgewähltem Benachrichtigungston
* Benachrichtigungen mit Bildern als Anhang

### 2. Voraussetzungen

- IP-Symcon ab Version 5.1
- Pushover Benutzerkonto

### 3. Software-Installation

Sie benötigen vom Entwickler entsprechende Zugangsdaten zur Nutzung des Moduls.  

Bei kommerzieller Nutzung (z.B. als Einrichter oder Integrator) wenden Sie sich bitte zunächst an den Autor.

Bei privater Nutzung:

Über das Modul-Control folgende URL hinzufügen: `https://username:password@git.ubittner.de/ubittner/Pushover.git`

### 4. Einrichten der Instanz in IP-Symcon

- In IP-Symcon an beliebiger Stelle `Instanz hinzufügen` auswählen und `Pushover` auswählen, welches unter dem Hersteller `Pushover` aufgeführt ist. Es wird eine Instanz angelegt, in der die Eigenschaften zur Benachrichtigung festgelegt werden können.  

__Konfigurationsseite__:

Name | Beschreibung
----------------------------------- | ---------------------------------------------
(0) Instanzinformationen            | Informationen zu der Instanz, Versionsnummer, Buildnummer
(2) Pushover Konfiguration          | Pushover Konfiguration, API Token, Benutzerschlüssel, etc.

Gebens Sie Ihren Pushover API Token und Ihren Benutzerschlüssel an.

Wählen Sie den Benachrichtigungston und die Zustellpriorität aus.


Sie können den Vorgang für weitere Pushover Instanzen wiederholen.

##### Hinweis:

Wird keine Gerätekennung aktiviert und ist die Liste der Endgeräte leer, so wird die Benachrichtung an alle dem Pushover Konto zugewiesenen Endgeräte versendet.

Soll die Benachrichtigung nur an ein oder mehrere, ausgewählte Endgeräte versendet werden, so aktivieren Sie die Gerätekennung und fügen die entsprechenden Endgeräte der Liste hinzu. 

__Benachrichtigungstöne__:

Benachrichtigungston | Beschreibung
---------------------|------------------------------
pushover             | Pushover (default)    
bike                 | Bike    
bugle                | Bugle    
cashregister         | Cash Register    
classical            | Classical    
cosmic               | Cosmic    
falling              | Falling    
gamelan              | Gamelan    
incoming             | Incoming    
intermission         | Intermission    
magic                | Magic    
mechanical           | Mechanical    
pianobar             | Piano Bar    
siren                | Siren    
spacealarm           | Space Alarm    
tugboat              | Tug Boat    
alien                | Alien Alarm (long)    
climb                | Climb (long)    
persistent           | Persistent (long)    
echo                 | Pushover Echo (long)    
updown               | Up Down (long)    
none                 | None (silent)

__Zustellpriorität__:

Zustellpriorität | Beschreibung
-----------------| ----------------------------
-2               | Nachrichten werden als niedrigste Priorität angesehen und erzeugen keine Benachrichtigung.
-1               | Nachrichten werden als niedrige Priorität betrachtet und erzeugen keine Geräusche oder Vibrationen, erzeugen aber trotzdem eine Popup-/Scrolling-Benachrichtigung.
 0               | Nachrichten, die ohne Prioritätsparameter oder mit dem Parameter 0 gesendet werden, haben die Standardpriorität. Diese Meldungen lösen Geräusche und Vibrationen aus und zeigen eine Warnung entsprechend den Geräteeinstellungen des Benutzers an.
 1               | Nachrichten mit hoher Priorität, die die Ruhezeiten eines Benutzers umgehen. Diese Nachrichten werden immer einen Ton abspielen und vibrieren.
 2               | Emergency-Priority-Benachrichtigungen sind ähnlich wie High-Priority-Benachrichtigungen, werden aber solange wiederholt, bis die Benachrichtigung vom Benutzer bestätigt wird.
 
### 5. Statusvariablen und Profile

##### Statusvariablen:

Zur zeit werden keine Statusvariablen verwendet.

##### Profile:

Nachfolgende Profile werden zusätzlichen hinzugefügt:

Es werden keine zusätzliche Profile hinzugefügt.

### 6. WebFront

Die Instanz wird im WebFront angezeigt und hat keine weitere Funktion.

### 7. PHP-Befehlsreferenz

Präfix des Moduls `PO` (Pushover)

`PO_SendPushoverNotification(integer $InstanzID, string $Titel, string $Nachricht)`

Versendet eine Nachricht mit Titel an die im Instanzeditor konfigurierten Geräte.

`PO_SendPushoverNotificationEx(integer $InstanzID, string $Titel, string $Nachricht, string $Benachrichtigungston, integer $Zustellpriorität, string $Endgerätename)`

Versendet eine Nachricht mit Parametern an ein oder mehrere Endgerät(e).

`PO_SendPushoverImageAttachmentEx(integer $InstanzID, string $Titel, string $Nachricht, string $Benachrichtigungston, integer $Zustellpriorität, string $Endgerätename, integer $BildID)`

Versendet eine Nachricht mit Parametern inklusive Bildanhang an ein oder mehrere Endgeräte.