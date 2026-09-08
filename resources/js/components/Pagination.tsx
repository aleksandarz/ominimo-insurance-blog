interface Props {
    currentPage: number;
    lastPage: number;
    from: number | null;
    to: number | null;
    total: number;
    onChange: (page: number) => void;
}

const mobileEnabled =
    'inline-flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-700 focus:outline-none active:bg-gray-100 active:text-gray-800 transition ease-in-out duration-150 hover:bg-gray-100';
const mobileDisabled =
    'inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 cursor-not-allowed leading-5 rounded-md';

const numberLink =
    'inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 focus:outline-none active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 hover:bg-gray-100';
const numberCurrent =
    'inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 cursor-default leading-5';

const chevronBase = 'inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 leading-5';
const chevronEnabled = `${chevronBase} hover:text-gray-400 focus:outline-none active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150 hover:bg-gray-100`;
const chevronDisabled = `${chevronBase} cursor-not-allowed`;

export default function Pagination({ currentPage, lastPage, from, to, total, onChange }: Props) {
    if (lastPage <= 1) {
        return null;
    }

    const onFirst = currentPage <= 1;
    const onLast = currentPage >= lastPage;
    const pages = Array.from({ length: lastPage }, (_, i) => i + 1);

    return (
        <nav role="navigation" aria-label="Pagination Navigation">
            <div className="flex gap-2 items-center justify-between sm:hidden">
                <button type="button" onClick={() => onChange(currentPage - 1)} disabled={onFirst} className={onFirst ? mobileDisabled : mobileEnabled}>
                    Previous
                </button>
                <button type="button" onClick={() => onChange(currentPage + 1)} disabled={onLast} className={onLast ? mobileDisabled : mobileEnabled}>
                    Next
                </button>
            </div>

            <div className="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">
                <div>
                    <p className="text-sm text-gray-700 leading-5">
                        Showing <span className="font-medium">{from ?? 0}</span> to <span className="font-medium">{to ?? 0}</span> of{' '}
                        <span className="font-medium">{total}</span> results
                    </p>
                </div>

                <div>
                    <span className="inline-flex shadow-sm rounded-md">
                        <button
                            type="button"
                            onClick={() => onChange(currentPage - 1)}
                            disabled={onFirst}
                            aria-label="Previous"
                            className={`${onFirst ? chevronDisabled : chevronEnabled} rounded-l-md`}
                        >
                            <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fillRule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clipRule="evenodd"
                                />
                            </svg>
                        </button>

                        {pages.map((p) =>
                            p === currentPage ? (
                                <span key={p} aria-current="page" className={numberCurrent}>
                                    {p}
                                </span>
                            ) : (
                                <button
                                    key={p}
                                    type="button"
                                    onClick={() => onChange(p)}
                                    aria-label={`Go to page ${p}`}
                                    className={numberLink}
                                >
                                    {p}
                                </button>
                            )
                        )}

                        <button
                            type="button"
                            onClick={() => onChange(currentPage + 1)}
                            disabled={onLast}
                            aria-label="Next"
                            className={`${onLast ? chevronDisabled : chevronEnabled} -ml-px rounded-r-md`}
                        >
                            <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fillRule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clipRule="evenodd"
                                />
                            </svg>
                        </button>
                    </span>
                </div>
            </div>
        </nav>
    );
}
