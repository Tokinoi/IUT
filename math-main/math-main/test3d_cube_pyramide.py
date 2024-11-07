import matplotlib.pyplot as plt
import numpy as np
import matplotlib.animation as animation
import matplotlib.patches as patches
import math
from copy import *

def make_sphere(nb_merid: int, size: float):
    theta = np.linspace(0, 2 * np.pi, nb_merid)
    phi = np.linspace(0, np.pi, nb_merid)
    theta, phi = np.meshgrid(theta, phi)

    x = size * np.sin(phi) * np.cos(theta)
    y = size * np.sin(phi) * np.sin(theta)
    z = size * np.cos(phi)

    return x, y, z

import matplotlib.pyplot as plt

from mpl_toolkits.mplot3d import axes3d



fig = plt.figure()
ax = fig.add_subplot(projection='3d')
SommetsCube=[ (x,y,z) for z in range(2) for y in range(2) for x in range(2) ]
print(SommetsCube)
OrdreCube=[0,1,3,2,0,4,5,7,6,2,0,4,5,1,3,7,6,4]
SommetsPyramide =  [ (3,0,0) , (3 ,1,0) , (2,1,0), (2,0,0) , (2.5 , 0.5 , 0.5) ]
OrdrePyramide = [ 0,1,2,3,4,0,3,4,1,2,4]

nb_meridians = 10
sphere_size = 1.5
sphere = ax.plot_surface(*make_sphere(nb_meridians, sphere_size), color='r', alpha=0.6)



# Grab some example data and plot a basic wireframe.
##X, Y, Z = axes3d.get_test_data(0.05)
##ax.plot_wireframe(X, Y, Z, rstride=5, cstride=10)
cube=ax.plot([SommetsCube[i][0] for i in OrdreCube],[SommetsCube[i][1] for i in OrdreCube],[SommetsCube[i][2] for i in OrdreCube])
pyramide=ax.plot([SommetsPyramide[i][0] for i in OrdrePyramide],[SommetsPyramide[i][1] for i in OrdrePyramide],[SommetsPyramide[i][2] for i in OrdrePyramide])
# Set the axis labels
##ax.set_xlabel('x')
##ax.set_ylabel('y')
##ax.set_zlabel('z')
ax.set_xlim(0,3)
ax.set_ylim(0,3)
ax.set_zlim(0,3)
ax.set_axis_off()


##v= np.array([
##    [math.cos(2*k*math.pi/10),math.sin(2*k*math.pi/10)] for k in range(10) ]  )
##
##w=v
##x=v

#la fonction rot retourne la matrice de la rotation
def rotx(angle):
    return np.array([[math.cos(angle),-math.sin(angle),0],[math.sin(angle),math.cos(angle),0],[0,0,1]])


def hom(k):
    return np.array([[k,0,0],[0,k,0],[0,0,k]])


    

# Rotate the axes and update
for angle in range(0, 360*4 + 1):
    # Normalize the angle to the range [-180, 180] for display
    angle_norm = (angle + 180) % 360 - 180

    # Cycle through a full rotation of elevation, then azimuth, roll, and all
    elev = azim = roll = 0
    if angle <= 360:
        elev = angle_norm
    elif angle <= 360*2:
        azim = angle_norm
    elif angle <= 360*3:
        roll = angle_norm
    else:
        elev = azim = roll = angle_norm

    # Update the axis view and title
    ax.view_init(elev, azim, roll)
    plt.title('Elevation: %d°, Azimuth: %d°, Roll: %d°' % (elev, azim, roll))

    plt.draw()
    plt.pause(.025)
