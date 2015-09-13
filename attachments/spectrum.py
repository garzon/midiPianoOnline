import matplotlib.pyplot as plt
from scipy.io import wavfile
from scipy.fftpack import fft
from pylab import *
import numpy, sys

fs, data = wavfile.read(sys.argv[1])
a = data
b=[(ele/2**8.)*2-1 for ele in a]
c = fft(b)
d = len(c)/2
c = c[:(d-1)]

rate = 44100.0
ratio = d*2.0/rate
standardFreq = 0
standardVol = -99999999
for i,j in enumerate(c):
	if i==0: continue
	if abs(j) > standardVol:
		standardVol = abs(j)
		standardFreq = i/ratio

res = [[i/ratio, abs(val)/standardVol, numpy.angle(val)] for(i, val) in enumerate(c) if abs(val)/standardVol > 0.01]
print res
