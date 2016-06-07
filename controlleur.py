#importation des librairies utilise
import serial
from threading import Thread
import re, sys, signal, os, datetime, time, requests
import RPi.GPIO as GPIO

# Librairies local
from utilities import *
from RFID import *
from distance import *
from init import *


#Declaration des constante
BITRATE = 9600
PINLED = 40
GPIO_TRIGGER = 16
GPIO_ECHO = 18
portUsbRFID = '/dev/ttyACM0'
#TIMEFORMAT = "%Y-%m-%dT%H:%M:%S.000Z"
D_STREAM_NAME = "DISTANCE"
R_STREAM_NAME = "RFID"
API_KEY = '0cf6abec0e9ea8f4bd8f2d267bb2eacf'
MARGE_FondBLI = 2
data = 'apiKey=0cf6abec0e9ea8f4bd8f2d267bb2eacf&deviceId=07966069e262d4bd198568352c3a4318&streamId='
headers = {
    'Content-Type': 'application/x-www-form-urlencoded',
    'Accept': 'text/html'
    }
sendData_isActif = False
TagRFID = ''


clear = lambda: os.system('clear')
now = datetime.datetime.now()



def setTagRFID(_TagRFID):
    global TagRFID
    TagRFID = _TagRFID

def sendData(distance):
    sendData_isActif = True
    SD_data = data
    global TagRFID
    if len(TagRFID) == 0:
    
        #creation chaine de caractere data
        SD_data += D_STREAM_NAME
        SD_data += '&valeur='
        SD_data += str(distance)

        print "\nEnvoi Distance"
        r = requests.post('http://testm2x.herokuapp.com/ws/postMessage.php', headers=headers, data=SD_data)
        
        print '='*40
        print "code de status : %d" % r.status_code
        print r.content
        print SD_data
        print '='*40
        print TagRFID
        
        SD_data = ' ' # clear data
        
    elif len(TagRFID) > 0:
        
        #creation chaine de caractere data
        SD_data += D_STREAM_NAME
        SD_data += ';'
        SD_data += R_STREAM_NAME
        SD_data += '&valeur='
        SD_data += str(distance)
        SD_data += ';'
        SD_data += TagRFID
        
        print "\nEnvoi Distance + RFID"
        r = requests.post('http://testm2x.herokuapp.com/ws/postMessage.php', headers=headers, data=SD_data)
    
        print '='*40
        print "code de status : %d" % r.status_code
        print r.content
        print SD_data
        print '='*40
        print TagRFID
        
        TagRFID = '' # clear TagRFID
        SD_data = '' # clear data
        
    else :
        print "Something wrong happened  with TagRFID"
    
    sendData_isActif = False

    
##############################################
#####      DEBUT THREAD LECTURERFID      #####
##############################################

class thread_lectureRFID(Thread):
    #Thread charge de detecte une puce RFID et de l'envoye au HUB
    #connecte pour pouvoir traite l'id.
    def run(self):
        #Code a executer pendant l'execution du thread
        if __name__ == '__main__':
            
            while True:
                setTagRFID(lectureRFID(serialRFID, '%Y-%m-%d %H:%M:%S %Z', PINLED)) #Lecture du tag RFID
                print 'RFID!' #Affiche la detection d'un tag RFID
                
                time.sleep(0.2) #ralenti le thread pour eviter de flood la console
                
##############################################
#####       FIN THREAD LECTURERFID       #####
##############################################




###################################################
#####      DEBUT THREAD LECTURERDISTANCE      #####
###################################################

class lectureDISTANCE(Thread):

    #Thread charge de detecte une puce RFID et de l'envoye au HUB
    #connecte pour pouvoir traite l'id.
        
    def run(self):
    
    
        #Code a executer pendant l'execution du thread
        
        FondBLI = lectureDistanceFondBLI(GPIO_TRIGGER, GPIO_ECHO) + MARGE_FondBLI
        
        while True:
            _distance = lectureDistance(16, 18)
            if (_distance < FondBLI ):
                if (_distance <= 10):
                    switchOnLed(PINLED)
                    print _distance
                else:
                    switchOffLed(PINLED)
                time.sleep(0.2)
                
                # ENVOI DONNES HEROKU
                if sendData_isActif == False :
                    sendData(_distance)
                else:
                    print "envoi deja en cours"
            
            time.sleep(0.2) #ralenti le thread pour eviter de flood la console
###################################################
#####       FIN THREAD LECTURERDISTANCE       #####
###################################################

clear()

#initialisation des diferents ports
gpioSetup(GPIO_TRIGGER,GPIO_ECHO)
serialRFID = serialSetup(portUsbRFID, BITRATE)

# Allow module to settle
time.sleep(0.5)

# Creation des threads
thread_LR = thread_lectureRFID()
thread_LD = lectureDISTANCE()

# Lancement des threads
thread_LR.start()
thread_LD.start()

# Attend que les threads se terminent
thread_LR.join()
thread_LD.join()
