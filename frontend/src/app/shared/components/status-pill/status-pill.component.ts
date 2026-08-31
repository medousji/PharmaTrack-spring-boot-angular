import { Component, input } from '@angular/core';

import type { Tone } from '@shared/ui/status-tone';

@Component({
  selector: 'app-status-pill',
  standalone: true,
  imports: [],
  templateUrl: './status-pill.component.html',
  styleUrl: './status-pill.component.css',
})
export class StatusPillComponent {
  readonly label = input.required<string>();
  readonly tone = input<Tone>('gray');
  readonly size = input<'sm' | 'lg'>('sm');
}