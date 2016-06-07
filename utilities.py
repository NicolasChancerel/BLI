import re, sys, signal, os, datetime, time
import RPi.GPIO as GPIO

def sleep(duration):
    time.sleep(duration)

def blinkLed(_gpioOutpout, _duree, _frequence):
    _frequence = _frequence + 0.01
    _frequence = float((1/_frequence)/2)
    timeStart = time.time()
    GPIO.setup(_gpioOutpout, GPIO.OUT)
    while (time.time() - timeStart)  <= _duree:
        GPIO.output(_gpioOutpout, GPIO.HIGH)
        sleep(_frequence)
        GPIO.output(_gpioOutpout, GPIO.LOW)
        sleep(_frequence)

def switchOnLedDuration(_gpioOutpout, _duration):
    isOff = True 
    timeStart = time.time()
    GPIO.setup(_gpioOutpout, GPIO.OUT)
    while isOff:
        if ((time.time() - timeStart) <= _duration):
            GPIO.output(_gpioOutpout, GPIO.HIGH)
        else:
            GPIO.output(_gpioOutpout, GPIO.LOW)
            isOff = False
    isOff = True

def switchOnLed(_gpioOutpout):
    GPIO.setup(_gpioOutpout, GPIO.OUT)
    GPIO.output(_gpioOutpout, GPIO.HIGH)

def switchOffLed(_gpioOutpout):
    GPIO.setup(_gpioOutpout, GPIO.OUT)
    GPIO.output(_gpioOutpout, GPIO.LOW)