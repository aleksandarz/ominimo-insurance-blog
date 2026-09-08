interface Props {
    children: React.ReactNode;
}

export default function FlashMessage({ children }: Props) {
    return (
        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {children}
        </div>
    );
}
