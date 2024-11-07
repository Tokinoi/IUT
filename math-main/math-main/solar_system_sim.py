import matplotlib.pyplot as plt
import numpy as np
import matplotlib.animation as animation
import matplotlib.patches as patches
import math
from random import *
from copy import *


fig = plt.figure()
ax = fig.add_subplot(111,aspect='equal')
ax.set_xlim(-20,20)
ax.set_ylim(-20,20)

nb_planet = randint(3,10)


planet_list = []
#Le tableau v ci-dessous contient les coordonnées initiales des 3 sommets du triangle
for i in range(nb_planet):
   distance = math.exp(random()*4 +4)
   revolution = (distance**3/ (25) )**0.5
   planet = (np.array([ [0.8*math.cos(2*k*math.pi/8),0.8*math.sin(2*k*math.pi/8)] for k in range(8) ]  ))
   patch = patches.Polygon(planet,closed=True, fc='y',ec='black')
   planet_list.append((planet,patch,distance,revolution))
   ax.add_patch(patch)


def anim(planete,i):
    k=1

    astre,patch,distance,revolution = planete

    for j in range(8):
        astre[j]=np.dot(rot(i),astre[j])
        astre[j]+=np.dot(rot(i/revolution*10),np.array([distance/100,0]))
        

    patch.set_xy(astre)
    for j in range(8):
        
        astre[j]-=np.dot(rot(i/revolution*10),np.array([distance/100,0]))

# lune1_planete1=np.array([
#     [0.3*math.cos(2*k*math.pi/8),0.3*math.sin(2*k*math.pi/8)] for k in range(8) ]  )


# planete2=np.array([
#     [0.6*math.cos(2*k*math.pi/8),0.6*math.sin(2*k*math.pi/8)] for k in range(8) ]  )
etoile=np.array([
    [math.cos(2*k*math.pi/10),math.sin(2*k*math.pi/10)] for k in range(10) ]  )

#la fonction rot retourne la matrice de la rotation
def rot(angle):
    return np.array([[math.cos(angle),-math.sin(angle)],[math.sin(angle),math.cos(angle)]])


def hom(k):
    return np.array([[k,0],[0,k]])
    
patch = patches.Polygon(etoile,closed=True, fc='y', ec='black')
ax.add_patch(patch)

def init():
   a=[]
   a.append(patch)
   for el in planet_list:
    a.append(el[1])
   return a

def animate(i):
    k=1
    for planet in planet_list:
      anim(planet,i)
    a=[]
    a.append(patch)
    for el in planet_list:
      a.append(el[1])
    return a 
    
   #  for j in range(8):
   #      planete1[j]=np.dot(rot(k/10),planete1[j])
   #      planete1[j]+=np.dot(rot(i/200),np.array([7,0]))
   #      lune1_planete1[j]+=np.dot(rot(i/200),np.array([7,0]))
   #      lune1_planete1[j]+=np.dot(rot(i/25),np.array([2,0]))

   #  patch.set_xy(planete1)
   #  patch4.set_xy(lune1_planete1)
   #  for j in range(8):
        
   #      planete1[j]-=np.dot(rot(i/200),np.array([7,0]))
   #      lune1_planete1[j]-=np.dot(rot(i/25),np.array([2,0]))
   #      lune1_planete1[j]-=np.dot(rot(i/200),np.array([7,0]))
        
        
   #  for j in range(8):
   #      planete2[j]=np.dot(rot(k/50),planete2[j])
   #      planete2[j]+=np.dot(rot(i/350),np.array([14,0]))
        

   #  patch2.set_xy(planete2)
   #  for j in range(8):
        
   #      planete2[j]-=np.dot(rot(i/350),np.array([14,0]))
   #  return patch,patch2,patch4

ani = animation.FuncAnimation(fig, animate, np.arange(3000), init_func=init,
                              interval=15, blit=True,repeat=False)
plt.show()


