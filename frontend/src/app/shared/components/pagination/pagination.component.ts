import { Component, computed, input, output } from '@angular/core';

@Component({
  selector: 'app-pagination',
  standalone: true,
  imports: [],
  templateUrl: './pagination.component.html',
  styleUrl: './pagination.component.css',
})
export class PaginationComponent {
  readonly page = input.required<number>();
  readonly totalPages = input.required<number>();
  readonly totalElements = input.required<number>();
  readonly itemLabel = input('élément(s)');

  readonly pageChange = output<number>();

  readonly canPrev = computed(() => this.page() > 0);
  readonly canNext = computed(() => this.page() < this.totalPages() - 1);

  prev(): void {
    this.pageChange.emit(this.page() - 1);
  }

  next(): void {
    this.pageChange.emit(this.page() + 1);
  }
}