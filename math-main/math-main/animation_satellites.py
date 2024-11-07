import matplotlib.pyplot as plt
import numpy as np
import matplotlib.animation as animation
import matplotlib.patches as patches
import math
from copy import *


fig = plt.figure()
ax = fig.add_subplot(111)
ax.set_xlim(-20,20)
ax.set_ylim(-20,20)

#Le tableau v ci-dessous contient les coordonnées initiales des 3 sommets du triangle
v= np.array([
    [math.cos(2*k*math.pi/10),math.sin(2*k*math.pi/10)] for k in range(10) ]  )

Mercure=v
Sun=v
Earth=v

#la fonction rot retourne la matrice de la rotation
def rot(angle):
    return np.array([[math.cos(angle),-math.sin(angle)],[math.sin(angle),math.cos(angle)]])
for k in range(10):
    v[k]=np.dot(rot(math.pi/4),v[k])

def hom(k):
    return np.array([[k,0],[0,k]])


    
    
patch = patches.Polygon(v,closed=True, fc='blue', ec='b') #Venus
patch2 = patches.Polygon(Mercure,closed=True, fc='red', ec='black') #Mercury
patch4 = patches.Polygon(Earth,closed=True, fc='yellow', ec='black') #Earth
patch3 = patches.Polygon(Sun,closed=True, fc='green', ec='b') #Sun 
ax.add_patch(patch)
ax.add_patch(patch2)
ax.add_patch(patch3)
ax.add_patch(patch4)
def init():
    return patch,patch2,patch4

def animate(i):
    k=1

    for j in range(10):
        v[j]=np.dot(rot(k/60),v[j])
        v[j]+=np.dot(rot(i/200),np.array([7,0]))
        

    patch.set_xy(v)

    for j in range(10):        
        v[j]-=np.dot(rot(i/200),np.array([7,0]))



    for j in range(10):
        Mercure[j]=np.dot(rot(k/200),Mercure[j])
        Mercure[j]+=np.dot(rot(i/25),np.array([4,0]))

    patch2.set_xy(Mercure)

    for j in range(10):
        
        Mercure[j]-=np.dot(rot(i/25),np.array([4,0]))

    for j in range(10):
        Earth[j]=np.dot(rot(k/50),Earth[j])
        Earth[j]+=np.dot(rot(i/500),np.array([4,0]))

    patch4.set_xy(Earth)

    for j in range(10):
        
        Earth[j]-=np.dot(rot(i/500),np.array([4,0]))
    return patch,patch2,patch3

ani = animation.FuncAnimation(fig, animate, np.arange(1000), init_func=init,
                              interval=15, blit=True,repeat=False)
plt.show()


