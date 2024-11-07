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

w=v
x=v
y=v

#la fonction rot retourne la matrice de la rotation
def rot(angle):
    return np.array([[math.cos(angle),-math.sin(angle)],[math.sin(angle),math.cos(angle)]])
for k in range(10):
    v[k]=np.dot(rot(math.pi/4),v[k])

def hom(k):
    return np.array([[k,0],[0,k]])

def do(astre,_patch,rev,spin,dist):
   for j in range(10):
        astre[j]=np.dot(rot(k/spin),astre[j])
        astre[j]+=np.dot(rot(rev/200),np.array([dist,0]))
   _patch.set_xy(astre)
   for j in range(10):
        astre[j]-=np.dot(rot(rev/200),np.array([dist,0]))

    
    
patch = patches.Polygon(v,closed=True, fc='r', ec='b')
patch2 = patches.Polygon(w,closed=True, fc='blue', ec='black')
patch3 = patches.Polygon(x,closed=True, fc='y', ec='b')
patch4 = patches.Polygon(y,closed=True, fc='green', ec='b')
ax.add_patch(patch)
ax.add_patch(patch2)
ax.add_patch(patch4)



ax.add_patch(patch3)

def init():
    return patch,patch2,patch4

def animate(i):
    k=1
    for j in range(10):
        v[j]=np.dot(rot(k),v[j])
        v[j]+=np.dot(rot(i/200),np.array([7,0]))
    patch.set_xy(v)
    for j in range(10):
        v[j]-=np.dot(rot(i/200),np.array([7,0]))

    do(w,patch2,1,200,2)
    do(y,patch4,20,2,9)

    return patch,patch2,patch4

ani = animation.FuncAnimation(fig, animate, np.arange(1000), init_func=init,
                              interval=15, blit=True,repeat=False)
plt.show()


