import matplotlib.pyplot as plt
import numpy as np
from mpl_toolkits.mplot3d import Axes3D
import matplotlib.animation as animation

def make_sphere(nb_merid: int, size: float):
    theta = np.linspace(0, 2 * np.pi, nb_merid)
    phi = np.linspace(0, np.pi, nb_merid)
    theta, phi = np.meshgrid(theta, phi)

    x = size * np.sin(phi) * np.cos(theta)
    y = size * np.sin(phi) * np.sin(theta)
    z = size * np.cos(phi)

    return x, y, z

def translate(sphere, vector):
    x, y, z = sphere
    x_translated = x + vector[0]
    y_translated = y + vector[1]
    z_translated = z + vector[2]
    return x_translated, y_translated, z_translated

def rotate(sphere, speed):
    x, y, z = sphere

    # Convert speed to radians
    angle_rad = np.radians(speed)

    # Create a rotation matrix around the y-axis
    rotation_matrix = np.array([
        [np.cos(angle_rad), 0, np.sin(angle_rad)],
        [0, 1, 0],
        [-np.sin(angle_rad), 0, np.cos(angle_rad)]
    ])

    # Apply rotation
    rotated_coordinates = np.dot(rotation_matrix, np.vstack([x.flatten(), y.flatten(), z.flatten()]))

    # Extract the rotated x, y, and z coordinates
    x_rotated, y_rotated, z_rotated = rotated_coordinates.reshape(3, *x.shape)

    return x_rotated, y_rotated, z_rotated

def plot_sphere(ax, sphere):
    x, y, z = sphere
    ax.set_xlim(-0.5, 0.5)
    ax.set_ylim(-0.5, 0.5)
    ax.set_zlim(-0.5, 0.5)
    ax.plot_surface(x, y, z, color='r', alpha=0.6)

def update(frame):
    ax.cla()  # Clear the previous frame
    global astre
    global sun 

    # Update the moon's position in a simple orbital rotation
    angle_rad = np.radians(frame)
    plot_sphere(ax,sun)
    for plan in astre: 
        orbit_radius = plan[1]
        position = np.array([orbit_radius * np.cos(angle_rad), orbit_radius * np.sin(angle_rad), 0])

        # Plot the sun and the moon
        rotate(plan[0],plan[1])
        plot_sphere(ax, translate(plan[0], position))



fig = plt.figure()
ax = fig.add_subplot(projection='3d')


nb_meridians = 10
sun_size = 0.2
moon_size = 0.05

# Create spheres for the sun and the moon
sun = make_sphere(nb_meridians, sun_size)
moon = make_sphere(nb_meridians, moon_size)
earth = make_sphere(nb_meridians,0.1)
# Set initial positions for the sun and the moon
sun_position = np.array([0, 0, 0])
moon_position = np.array([1.5, 0, 0])

# Store the sun and moon in a list
astre = [(moon,0.2),(earth,0.4)]

# Plot the sun and the moon
plot_sphere(ax, sun)
plot_sphere(ax, moon)

# Set axis limits


ani = animation.FuncAnimation(fig, update, frames=360, interval=50, blit=False)

plt.show()
