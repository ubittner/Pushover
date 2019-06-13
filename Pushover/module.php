<?php

/*
 * @module      Pushover
 *
 * @prefix      PO
 *
 * @file        module.php
 *
 * @author      Ulrich Bittner
 * @project     Ulrich Bittner
 * @copyright   (c) 2019
 * @license     CC BY-NC-SA 4.0
 *
 * @version     1.00-1
 * @date        2019-06-13, 18:00
 * @lastchange  2019-06-13, 18:00
 *
 * @see         https://git.ubittner.de/ubittner/Pushover.git
 *
 * @guids       Library
 *              {E1B42F7C-3733-404C-8E9D-037A9883AD43}
 *
 *              Module
 *              {E38905D3-37E3-4AB3-861A-0F41CFE60BC8}
 *
 * @changelog   2019-06-13, 18:00, initial module script version 1.00
 *
 */

// Declare
declare(strict_types=1);

// Class
class Pushover extends IPSModule
{
    /**
     * Creates this instance.
     *
     * @return bool|void
     */
    public function Create()
    {
        // Never delete this line!
        parent::Create();

        //#################### Register properties

        // Configuration
        $this->RegisterPropertyString('APIToken', '');
        $this->RegisterPropertyString('UserKey', '');
        $this->RegisterPropertyString('SoundType', 'pushover');
        $this->RegisterPropertyInteger('PriorityType', 1);
        $this->RegisterPropertyBoolean('UseDeviceIdentifier', false);
        $this->RegisterPropertyString('Devices', '[]');
        $this->RegisterPropertyString('Title', 'IP-Symcon Pushover');
        $this->RegisterPropertyString('Message', 'Dies ist eine Testnachricht von IP-Symcon!');
    }

    /**
     * Applies the changes to this instance.
     *
     * @return bool|void
     */
    public function ApplyChanges()
    {
        // Register messages
        // Base
        $this->RegisterMessage(0, IPS_KERNELSTARTED);

        // Never delete this line!
        parent::ApplyChanges();

        // Check runlevel
        if (IPS_GetKernelRunlevel() != KR_READY) {
            return;
        }

        // Check configuration
        $this->ValidateConfiguration();
    }

    /**
     * Checks the message sink.
     *
     * @param $TimeStamp
     * @param $SenderID
     * @param $Message
     * @param $Data
     *
     * @return bool|void
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        $this->SendDebug('MessageSink', 'SenderID: ' . $SenderID . ', Message: ' . $Message, 0);
        switch ($Message) {
            case IPS_KERNELSTARTED:
                $this->KernelReady();
                break;
        }
    }

    /**
     * Applies changes when the kernel is ready.
     */
    protected function KernelReady()
    {
        $this->ApplyChanges();
    }

    /**
     * Gets the configuration form.
     *
     * @return false|string
     */
    public function GetConfigurationForm()
    {
        $formdata = json_decode(file_get_contents(__DIR__ . '/form.json'));
        /*
        // Device list
        $devices = json_decode($this->ReadPropertyString('Devices'));
        if (!empty($devices)) {
            $status = true;
            foreach ($devices as $currentKey => $currentArray) {
                $rowColor = '';
                foreach ($devices as $searchKey => $searchArray) {
                    // Search for duplicate entries
                    if ($searchArray->Position == $currentArray->Position) {
                        if ($searchKey != $currentKey) {
                            $status = false;
                        }
                    }
                    if ($searchArray->DeviceName == $currentArray->DeviceName) {
                        if ($searchKey != $currentKey) {
                            $status = false;
                        }
                    }
                }
                // Check entries
                if ($currentArray->UseUtilisation == true) {
                    if ($currentArray->Position == '') {
                        $status = false;
                    }
                    if ($currentArray->DeviceName == '') {
                        $status = false;
                    }

                    if ($status == false) {
                        $rowColor = '#FFC0C0';
                        $this->SetStatus(205);
                    }
                }
                $formdata->elements[12]->values[] = ['rowColor' => $rowColor];
            }
        }
        */
        return json_encode($formdata);
    }

    //#################### Public

    /**
     * Sends a message via Pushover and uses the parameters from configuration form.
     *
     * @param string $Title
     * @param string $Message
     */
    public function SendPushoverNotification(string $Title, string $Message)
    {
        if ($this->ReadPropertyBoolean('UseDeviceIdentifier')) {
            $devices = json_decode($this->ReadPropertyString('Devices'));
            if (!empty($devices)) {
                foreach ($devices as $device) {
                    if (!empty($device->DeviceName) && $device->UseUtilisation == true) {
                        $this->SendPushoverNotificationEx($Title, $Message, $device->SoundType, $device->PriorityType, $device->DeviceName);
                    }
                }
            }
        } else {
            $sound = $this->ReadPropertyString('SoundType');
            $priority = $this->ReadPropertyInteger('PriorityType');
            $this->SendPushoverNotificationEx($Title, $Message, $sound, $priority, '');
        }
    }

    /**
     * Sends a message via Pushover.
     *
     * @param string $Title
     * @param string $Message
     * @param string $Sound
     * @param int    $Priority
     * @param string $Device
     *
     * @return mixed
     */
    public function SendPushoverNotificationEx(string $Title, string $Message, string $Sound, int $Priority, string $Device)
    {
        $token = $this->ReadPropertyString('APIToken');
        if (empty($token)) {
            return false;
        }
        $user = $this->ReadPropertyString('UserKey');
        if (empty($user)) {
            return false;
        }
        if ($Title == '') {
            $Title = $this->Translate('Unbekannter Titel!');
        }
        if ($Message == '') {
            $Message = $this->Translate('Nachricht hat keinen Inhalt!');
        }
        $retry = '30';
        $expire = '3600';
        // Postfields
        $postfields = [
            'token' => $token,
            'user' => $user,
            'title' => $Title,
            'message' => $Message,
            'sound' => $Sound,
            'priority' => $Priority,
            'retry' => $retry,
            'expire' => $expire,];
        if ($Device != '') {
            $postfields += ['device' => $Device];
        }
        // Send message
        curl_setopt_array($ch = curl_init(), [
            CURLOPT_URL => 'https://api.pushover.net/1/messages.json',
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_SAFE_UPLOAD => true,
            CURLOPT_RETURNTRANSFER => true,]);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * Sends a message via Pushover including an image as attachment.
     *
     * @param string $Title
     * @param string $Message
     * @param string $Sound
     * @param int    $Priority
     * @param string $Device
     * @param int    $ImageID
     *
     * @return bool|string
     */
    public function SendPushoverImageAttachmentEx(string $Title, string $Message, string $Sound, int $Priority, string $Device, int $ImageID)
    {
        $token = $this->ReadPropertyString('APIToken');
        if (empty($token)) {
            return false;
        }
        $user = $this->ReadPropertyString('UserKey');
        if (empty($user)) {
            return false;
        }
        if ($Title == '') {
            $Title = $this->Translate('Unbekannter Titel!');
        }
        if ($Message == '') {
            $Message = $this->Translate('Nachricht hat keinen Inhalt!');
        }
        $retry = '30';
        $expire = '3600';
        // Postfields
        $postfields = [
            'token' => $token,
            'user' => $user,
            'title' => $Title,
            'message' => $Message,
            'sound' => $Sound,
            'priority' => $Priority,
            'retry' => $retry,
            'expire' => $expire,
        ];
        // Device
        if ($Device != '') {
            $postfields += ['device' => $Device];
        }
        // Image
        if (IPS_GetObject($ImageID)['ObjectIdent'] == 'Image') {
            $image = base64_decode(IPS_GetMediaContent($ImageID));
            $imagePath = IPS_GetKernelDir() . 'media' . DIRECTORY_SEPARATOR . 'PO_' . $this->InstanceID . '_Image.jpg';
            file_put_contents($imagePath, $image);
            $curlFile = new CURLFile(IPS_GetKernelDir() . 'media' . DIRECTORY_SEPARATOR . 'PO_' . $this->InstanceID . '_Image.jpg', 'image/jpg', 'Doorbell');
            $postfields += ['attachment' => $curlFile];
        }
        // Send message
        curl_setopt_array($ch = curl_init(), [
            CURLOPT_URL => 'https://api.pushover.net/1/messages.json',
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_SAFE_UPLOAD => true,
            CURLOPT_RETURNTRANSFER => true,]);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    //#################### Private

    /**
     * Validates the configuration form.
     */
    private function ValidateConfiguration()
    {
        $this->SetStatus(102);

        // Check device identifier
        if ($this->ReadPropertyBoolean('UseDeviceIdentifier')) {
            $devices = json_decode($this->ReadPropertyString('Devices'));
            if (empty($devices)) {
                $this->SetStatus(203);
            } else {
                $status = false;
                foreach ($devices as $device) {
                    if ($device->UseUtilisation == true) {
                        $status = true;
                    }
                }
                if ($status == false) {
                    $this->SetStatus(204);
                }
            }
        }
        // Check user key
        if (empty($this->ReadPropertyString('UserKey'))) {
            $this->SetStatus(202);
        }
        // Check API token
        if (empty($this->ReadPropertyString('APIToken'))) {
            $this->SetStatus(201);
        }
    }
}