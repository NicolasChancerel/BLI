#!/usr/bin/python2

#importation des librairies utilise
import re, sys, signal, os, datetime, time, serial
import RPi.GPIO as GPIO
from threading import Thread
from utilities import *


#######################################
#####      DEBUT LECTURERFID      #####
#######################################

def lectureRFID(serialRFID, fmt, pinLed): # (port ou est brancher le lecteur RFID, bitrate du lecteur RFID, Led a allumer)

        buffer = ''                               #creation du buffer                 
        rfidPattern = re. compile(b'[\W_]+')      #creation patterne RFID

        while True:
          # Read data from RFID reader
          buffer = buffer + serialRFID.read(serialRFID.inWaiting())     #lecture tag RFID
          
          if '\n' in buffer:                              #
            lines = buffer.split('\n')                    #
            last_received = lines[-2]                     #
            tagRFID = rfidPattern.sub('', last_received)  #

            if tagRFID:                                          #Affichage des informations :
            
                #print "\n"
                #print "=" *20
                #print tagRFID                                    #TagRFID
                #print (datetime.datetime.now()).strftime(fmt)  #Date et heure
                #print "=" *20
                #print "\n"
                
                blinkLed(pinLed, 1, 15)                          #blinkLed(pin GPIO, temps , nb changement d'etat vers 1 /s) 0 a 60
                
            # Clear buffer
            buffer = ''         #nettoyage du buffer
            lines = ''          #nettoyage de la variable lines 
            
            return tagRFID

#######################################
#####       FIN LECTURERFID       #####
#######################################