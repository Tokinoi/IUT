mod entity;

use sfml::{
    graphics::{Transformable, Color, RenderTarget, RenderWindow, Shape, CircleShape},
    window::{ContextSettings, Event, Key, Style},
};
use crate::entity::{Entity, get_color};
use crate::entity::State;

const WIDTH: u32 = 800;
const HEIGHT: u32 = 600;
const MAX_ENTITIES: i32 = 200;


fn main() {
    let mut settings = ContextSettings::default();
    settings.antialiasing_level = 6;

    let mut window = RenderWindow::new(
        (WIDTH, HEIGHT),
        "Social Simulation",
        Style::CLOSE,
        &Default::default(),
    );
    let mut entities = Vec::new();
    for _ in 0..MAX_ENTITIES {
        let entity = Entity::init_random(HEIGHT, WIDTH, 0.2);
        // entity.create();
        entities.push(entity);
    }

    let mut vsync = true;
    window.set_vertical_sync_enabled(vsync);

    let mut shape = CircleShape::new(3f32, 20);
    shape.set_outline_color(Color::rgb(0, 0, 0));
    shape.set_outline_thickness(1.0f32);

    let mut paused = false;

    'main_loop: loop {
        while let Some(event) = window.poll_event() {
            match event {
                Event::Closed
                | Event::KeyPressed {
                    code: Key::Escape, ..
                } => break 'main_loop,
                Event::KeyPressed {
                    code: Key::S, ..
                } => {
                    vsync = !vsync;
                    window.set_vertical_sync_enabled(vsync)
                }
                Event::KeyPressed {
                    code: Key::P, ..
                } => paused = !paused,
                _ => {}
            }
        }

        if !paused {
            window.clear(Color::rgb(150, 150, 150));

            for i in 0..MAX_ENTITIES as usize {
                simulate_frame(&mut window, i, &mut shape, &mut entities);
            }
        }
        window.display();
    }
}

fn simulate_frame(window: &mut RenderWindow, index: usize, shape: &mut CircleShape, entities: &mut Vec<Entity>) {

    entities[index].update_state();
    let mut entity = entities[index].clone(); // Clone the entity

    if entity.state != State::Dead {
        entity.step();
        entity.check_outside();


        if entity.state == State::Ill {
            entity.spread(entities);
        }

    }
    shape.set_position(entity.get_position().to_tuple());
    entities[index] = entity.clone();
    shape.set_fill_color(get_color(entity.state));
    window.draw(shape);
}


